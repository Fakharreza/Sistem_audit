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
            
            // Hitung berapa soal yang sudah dijawab di domain ini
            foreach ($domain->questions as $q) {
                if (isset($existingResponses[$q->id]) && $existingResponses[$q->id]->score !== null) {
                    $answered++;
                }
            }

            // Tentukan Kelas CSS berdasarkan rasio jawaban
            if ($totalQuestions == 0 || $answered == 0) {
                $tabStatuses[$domain->id] = 'bg-gray-100 text-gray-500 border-gray-200'; // ABU-ABU (Kosong)
            } elseif ($answered < $totalQuestions) {
                $tabStatuses[$domain->id] = 'bg-yellow-100 text-yellow-700 border-yellow-300'; // KUNING (Sebagian)
            } else {
                $tabStatuses[$domain->id] = 'bg-blue-100 text-blue-700 border-blue-300'; // BIRU (Lengkap)
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
                
    
                $existing = \App\Models\AuditResponse::where('audit_id', $audit->id)
                                ->where('cobit_question_id', $questionId)->first();

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
