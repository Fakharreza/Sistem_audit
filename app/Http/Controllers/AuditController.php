<?php

namespace App\Http\Controllers;
use App\Models\Domain;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{

    public function dashboard()
    {
        $audits = Audit::where('user_id', Auth::id())
                       ->orderBy('created_at', 'desc')
                       ->get();

        return view('auditor.dashboard', compact('audits'));
    }
    public function create()
    {
        $domains = Domain::all();
        return view('auditor.create', compact('domains'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'audit_code' => 'required|string|max:255',
            'audit_date' => 'required|date',
            'domain_ids' => 'required|array',
        ]);

        $audit = Audit::create([
            'user_id' => Auth::id(),             
            'auditor_name' => Auth::user()->name, 
            'audit_code' => $request->audit_code,
            'audit_date' => $request->audit_date,
            'status' => 'draft',
        ]);

        $audit->domains()->attach($request->domain_ids);

        return redirect()->route('auditor.dashboard')
                         ->with('success', 'Sesi Audit berhasil dibuat! Silakan mulai pengisian kuesioner.');
    }

    public function showKuesioner(Audit $audit)
    {
        if ($audit->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke sesi audit ini.');
        }

        $audit->load('domains.questions');
        return view('auditor.kuesioner', compact('audit'));
    }
    public function storeKuesioner(Request $request, Audit $audit)
    {
        if ($audit->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*.score' => 'required|numeric|in:0,0.5,1', 
            'answers.*.notes' => 'nullable|string', 
            'answers.*.evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        foreach ($request->answers as $questionId => $answer) {
            
            $evidencePath = null;
            
            if (isset($answer['evidence'])) {
                $evidencePath = $answer['evidence']->store('evidences', 'public');
            }

            // Simpan ke tabel audit_responses
            \App\Models\AuditResponse::create([
                'audit_id' => $audit->id,
                'cobit_question_id' => $questionId,
                'score' => $answer['score'],
                'notes' => $answer['notes'] ?? null,
                'evidence_file' => $evidencePath, 
            ]);
        }

        $audit->update([
            'status' => 'completed'
        ]);

        // 5. Kembalikan ke dashboard
        return redirect()->route('auditor.dashboard')
                         ->with('success', 'Luar biasa! Kuesioner berhasil diselesaikan dan disimpan.');
    }
}
