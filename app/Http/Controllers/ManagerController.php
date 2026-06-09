<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use App\Models\Audit;
use App\Models\Criterion;
use App\Models\AuditResponse;
use App\Models\GapEvaluation; 
use App\Models\Domain; 
use App\Models\AuditProgress;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Audit::where('status', 'completed');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('audit_code', 'like', "%{$search}%")
                  ->orWhere('auditor_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('audit_date', $request->year);
        }

        $audits = $query->orderBy('updated_at', 'desc')->paginate(5)->withQueryString();

        foreach ($audits as $audit) {
            $responses = AuditResponse::with('question')->where('audit_id', $audit->id)->get();
            $grouped = $responses->groupBy('question.domain_id');

            $totalMaturity = 0;
            $domainCount = 0;

            foreach ($grouped as $domainId => $domainResponses) {
                $validResponses = $domainResponses->filter(function($r) { return $r->question != null; });
                if ($validResponses->isEmpty()) continue;

                $sorted = $validResponses->sortBy('question.capability_level');
                $maturity = 5; 
                foreach ($sorted as $resp) {
                    if ($resp->score < 1) { 
                        $maturity = $resp->question->capability_level - 1;
                        break;
                    }
                }
                $totalMaturity += $maturity;
                $domainCount++;
            }
            $audit->itml_score = $domainCount > 0 ? round($totalMaturity / $domainCount, 2) : 0;

            $hasGaps = AuditResponse::where('audit_id', $audit->id)->where('score', '<', 1)->exists();
            $isEvaluated = GapEvaluation::whereHas('auditResponse', function($query) use ($audit) {
                $query->where('audit_id', $audit->id);
            })->exists();
            
            $audit->can_see_result = (!$hasGaps) || ($hasGaps && $isEvaluated);
        }

        $domains = Domain::orderBy('code', 'asc')->get();
        $latestCompletedAudit = Audit::where('status', 'completed')->orderBy('updated_at', 'desc')->first(); 
        
        $chartLabels = [];
        $chartData = [];

        $latestResponses = collect();
        if ($latestCompletedAudit) {
            $latestResponses = AuditResponse::with('question')
                                ->where('audit_id', $latestCompletedAudit->id)
                                ->get();
        }

        foreach ($domains as $domain) {
            $chartLabels[] = $domain->code; 

            if ($latestCompletedAudit) {
                $domainResponses = $latestResponses->filter(function($item) use ($domain) {
                    return $item->question && $item->question->domain_id == $domain->id;
                });

                if ($domainResponses->isEmpty()) {
                    $chartData[] = 0; 
                } else {
                    $sortedResponses = $domainResponses->sortBy('question.capability_level');
                    $maturity = 5; 
                    foreach ($sortedResponses as $resp) {
                        if ($resp->score < 1) {
                            $maturity = $resp->question->capability_level - 1;
                            break;
                        }
                    }
                    $chartData[] = $maturity; 
                }
            } else {
                $chartData[] = 0;
            }
        }

        return view('manager.dashboard', compact('audits', 'chartLabels', 'chartData', 'latestCompletedAudit'));
    }

    public function evaluate(Audit $audit)
    {
        $gaps = AuditResponse::with('question.domain')
                    ->where('audit_id', $audit->id)
                    ->where('score', '<', 1)
                    ->get();

        if ($gaps->isEmpty()) {
            return redirect()->route('manager.audit.result', $audit->id)
                             ->with('success', 'Luar biasa! Audit ini mendapatkan nilai sempurna (Level 5) di semua area. Tidak perlu perhitungan prioritas SAW.');
        }

        $criteria = Criterion::all();
        return view('manager.evaluate', compact('audit', 'gaps', 'criteria'));
    }

    public function calculate(Request $request, Audit $audit)
    {
        $request->validate(['evaluations' => 'required|array']);
        $criteria = Criterion::all()->keyBy('id');

        foreach ($request->evaluations as $gapId => $crits) {
            foreach ($crits as $criterionId => $score) {
                $master = $criteria[$criterionId];
                GapEvaluation::updateOrCreate(
                    ['audit_response_id' => $gapId, 'criterion_id' => $criterionId],
                    ['score' => $score, 'weight_snapshot' => $master->weight, 'type_snapshot' => $master->type]
                );
            }
        }
        return redirect()->route('manager.audit.result', $audit->id);
    }
    
    public function showResult(Audit $audit)
    {
        $gaps = AuditResponse::with(['question.domain', 'gapEvaluations.criterion'])
                    ->where('audit_id', $audit->id)->where('score', '<', 1)->get();

        $evaluations = [];
        $snapshotCriteria = []; 

        foreach ($gaps as $gap) {
            foreach ($gap->gapEvaluations as $eval) {
                $evaluations[$gap->id][$eval->criterion_id] = $eval->score;

                if (!isset($snapshotCriteria[$eval->criterion_id])) {
                    $snapshotCriteria[$eval->criterion_id] = [
                        'id' => $eval->criterion_id,
                        'weight' => $eval->weight_snapshot ?? ($eval->criterion->weight ?? 0),
                        'type' => $eval->type_snapshot ?? ($eval->criterion->type ?? 'benefit'),
                        'name' => $eval->criterion->name ?? 'Kriteria (Telah Dihapus)'
                    ];
                }
            }
        }

        $hasGaps = AuditResponse::where('audit_id', $audit->id)->where('score', '<', 1)->exists();
        if ($hasGaps && empty($evaluations)) {
            return redirect()->route('manager.audit.evaluate', $audit->id);
        }

        $minMax = [];
        foreach ($snapshotCriteria as $criterion) {
            $values = [];
            foreach ($evaluations as $gapId => $crits) {
                $values[] = $crits[$criterion['id']] ?? 1; 
            }
            if(!empty($values)) {
                $minMax[$criterion['id']] = $criterion['type'] == 'benefit' ? max($values) : min($values);
            }
        }

        $results = [];
        foreach ($gaps as $gap) {
            $totalScore = 0;
            $normalizedScores = [];
            
            foreach ($snapshotCriteria as $criterion) {
                $x = $evaluations[$gap->id][$criterion['id']] ?? 1; 
                $weight = $criterion['weight']; 
                $minMaxValue = $minMax[$criterion['id']] ?? 1;
                if($minMaxValue == 0) $minMaxValue = 1; 
                
                $r = $criterion['type'] == 'benefit' ? ($x / $minMaxValue) : ($minMaxValue / $x);
                $normalizedScores[$criterion['id']] = $r;
                $totalScore += ($r * $weight);
            }
            $results[] = ['gap' => $gap, 'original' => $evaluations[$gap->id] ?? [], 'normalized' => $normalizedScores, 'final_score' => $totalScore];
        }
        
        usort($results, function($a, $b) { return $b['final_score'] <=> $a['final_score']; });

        $domainSawScores = [];
        foreach ($results as $res) {
            $domainName = $res['gap']->question->domain->code . ' - ' . $res['gap']->question->domain->name;
            if (!isset($domainSawScores[$domainName])) {
                $domainSawScores[$domainName] = $res['final_score'];
            } else {
                $domainSawScores[$domainName] = max($domainSawScores[$domainName], $res['final_score']);
            }
        }

        $allResponses = AuditResponse::with('question.domain')->where('audit_id', $audit->id)->get();
        $groupedByDomain = $allResponses->groupBy(function($item) {
            return $item->question->domain->code . ' - ' . $item->question->domain->name;
        });

        $roadmaps = [];
        foreach ($groupedByDomain as $domainName => $responses) {
            $sortedResponses = $responses->sortBy('question.capability_level');
            
            $currentMaturity = 5; 
            $targetLevel = 'Maksimal (5)'; 
            $recommendation = 'Kondisi sudah sangat optimal. Pertahankan performa tingkat tertinggi ini.';
            
            foreach ($sortedResponses as $resp) {
                if ($resp->score < 1) {
                    $targetLevel = $resp->question->capability_level;
                    $recommendation = $resp->question->description; 
                    $currentMaturity = $targetLevel - 1; 
                    break; 
                }
            }
            $roadmaps[] = [
                'domain' => $domainName, 'maturity' => $currentMaturity,
                'target_level' => $targetLevel, 'recommendation' => $recommendation,
                'sort_score' => $domainSawScores[$domainName] ?? 0
            ];
        }
        usort($roadmaps, function($a, $b) { return $b['sort_score'] <=> $a['sort_score']; });

        $criteria = $snapshotCriteria; 

        $totalMaturity = 0;
        foreach ($roadmaps as $rm) {
            $totalMaturity += $rm['maturity'];
        }
        $audit->itml_score = count($roadmaps) > 0 ? round($totalMaturity / count($roadmaps), 2) : 0;

        $progressNotes = AuditProgress::where('audit_id', $audit->id)->get()->keyBy('domain_name')->toArray();

        $allDomains = Domain::orderBy('code', 'asc')->get();
        $chartLabels = [];
        $chartData = [];

        $roadmapMaturities = [];
        foreach($roadmaps as $rm) {
            $code = explode(' - ', $rm['domain'])[0];
            $roadmapMaturities[$code] = $rm['maturity'];
        }

        foreach ($allDomains as $domain) {
            $chartLabels[] = $domain->code;
            $chartData[] = $roadmapMaturities[$domain->code] ?? 0;
        }

        return view('manager.result', compact('audit', 'results', 'criteria', 'roadmaps', 'progressNotes', 'chartLabels', 'chartData'));
    }

    public function storeProgress(Request $request, Audit $audit)
    {
        $request->validate([
            'domain_name' => 'required|string',
            'notes' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048' 
        ]);

        $dataToSave = ['notes' => $request->notes];

        $progress = AuditProgress::where('audit_id', $audit->id)
                                 ->where('domain_name', $request->domain_name)
                                 ->first();

        if ($request->delete_evidence == '1' && $progress && $progress->evidence_file) {
            Storage::disk('public')->delete($progress->evidence_file);
            $dataToSave['evidence_file'] = null; 
        }

        if ($request->hasFile('evidence')) {
            if ($progress && $progress->evidence_file && $request->delete_evidence != '1') {
                Storage::disk('public')->delete($progress->evidence_file);
            }
            
            $dataToSave['evidence_file'] = $request->file('evidence')->store('progress_evidences', 'public');
        }

        AuditProgress::updateOrCreate(
            ['audit_id' => $audit->id, 'domain_name' => $request->domain_name],
            $dataToSave
        );

        return redirect()->back()->with('success', 'Catatan progress & Bukti (Evidence) untuk ' . explode(' - ', $request->domain_name)[0] . ' berhasil diperbarui!');
    }
}