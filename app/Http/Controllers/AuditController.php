<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Audit::where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('audit_code', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->whereYear('audit_date', $request->year);
        }

        $audits = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();

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
            // Tambahkan unique:audits,audit_code
            'audit_code' => 'required|string|max:255|unique:audits,audit_code',
            'audit_date' => 'required|date',
            'domain_ids' => 'required|array',
        ], [
            // KUSTOM PESAN ERROR BAHASA INDONESIA BIAR CANTIK
            'audit_code.unique' => 'Gagal! Nama / Kode Audit ini sudah pernah digunakan. Silakan gunakan nama lain.',
            'audit_code.required' => 'Nama / Kode Audit wajib diisi.',
            'audit_date.required' => 'Tanggal pelaksanaan wajib diisi.',
            'domain_ids.required' => 'Silakan centang minimal 1 domain COBIT 2019 untuk dievaluasi.',
        ]);
        // ========================================================

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

        $audit->load(['domains.questions' => function ($query) {
            $query->orderBy('capability_level', 'asc')->orderBy('activity_code', 'asc');
        }]);

        $groupedData = [];
        foreach ($audit->domains as $domain) {
            $groupedByLevelAndActivity = $domain->questions->groupBy('capability_level')->map(function ($levelGroup) {
                return $levelGroup->groupBy('activity_code');
            });

            $groupedData[$domain->id] = [
                'domain_name' => $domain->code . ' - ' . $domain->name,
                'levels' => $groupedByLevelAndActivity
            ];
        }

        $existingResponses = \App\Models\AuditResponse::where('audit_id', $audit->id)
                                ->get()
                                ->keyBy('cobit_question_id');

        $tabStatuses = [];
        foreach ($audit->domains as $domain) {
            $totalQuestions = $domain->questions->count();
            $answered = 0;
            
            foreach ($domain->questions as $q) {
                if (isset($existingResponses[$q->id]) && $existingResponses[$q->id]->score !== null) {
                    $answered++;
                }
            }

            if ($totalQuestions == 0 || $answered == 0) {
                $tabStatuses[$domain->id] = 'bg-gray-100 text-gray-500 border-gray-200'; 
            } elseif ($answered < $totalQuestions) {
                $tabStatuses[$domain->id] = 'bg-yellow-100 text-yellow-700 border-yellow-300'; 
            } else {
                $tabStatuses[$domain->id] = 'bg-blue-100 text-blue-700 border-blue-300'; 
            }
        }
        $isReadOnly = $audit->status === 'completed';

        return view('auditor.kuesioner', compact('audit', 'groupedData', 'existingResponses', 'tabStatuses', 'isReadOnly'));
    }

    public function storeKuesioner(Request $request, Audit $audit)
    {
        if ($audit->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'answers' => 'nullable|array',
            'answers.*.score' => 'nullable|numeric', 
            'answers.*.notes' => 'nullable|string',
            'answers.*.evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $action = $request->input('action'); 

        foreach ($request->answers ?? [] as $questionId => $answer) {
            
            $hasScore = isset($answer['score']);
            $hasNotes = !empty($answer['notes']);
            $hasFile = isset($answer['evidence']);

            if ($hasScore || $hasNotes || $hasFile) {
                
                $dataToUpdate = [];
                
                if ($hasScore) $dataToUpdate['score'] = $answer['score'];
                if ($hasNotes) $dataToUpdate['notes'] = $answer['notes'];

                if ($hasFile) {
                    $dataToUpdate['evidence_file'] = $answer['evidence']->store('evidences', 'public');
                }

                \App\Models\AuditResponse::updateOrCreate(
                    [
                        'audit_id' => $audit->id,
                        'cobit_question_id' => $questionId
                    ],
                    $dataToUpdate
                );
            }
        }

        if ($action === 'submit') {
            $audit->update(['status' => 'completed']);
            return redirect()->route('auditor.dashboard')->with('success', 'Luar biasa! Kuesioner berhasil diselesaikan dan disimpan.');
        } else {
            return redirect()->back()->with('success', 'Draft berhasil disimpan! (Termasuk File Bukti Dukung)');
        }
    }
}