<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\Criterion;
use App\Models\AuditResponse;
use App\Models\GapEvaluation; 
use App\Models\Domain; 
use App\Models\AuditProgress; // <-- Tambahan Model Baru

class ManagerController extends Controller
{
   public function dashboard()
    {
        $audits = Audit::where('status', 'completed')
                       ->orderBy('updated_at', 'desc')
                       ->get();


        foreach ($audits as $audit) {
            $avg = AuditResponse::where('audit_id', $audit->id)->avg('score');
            $audit->itml_score = $avg ? round($avg * 5, 2) : 0;
        }

      
        $domains = Domain::orderBy('code', 'asc')->get();
        $latestCompletedAudit = $audits->first(); 
        
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
            return redirect()->route('manager.dashboard')
                             ->with('success', 'Luar biasa! Audit ini tidak memiliki gap. Tidak perlu perhitungan SAW.');
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

        if (empty($evaluations)) return redirect()->route('manager.audit.evaluate', $audit->id);

        $minMax = [];
        foreach ($snapshotCriteria as $criterion) {
            $values = [];
            foreach ($evaluations as $gapId => $crits) {
                $values[] = $crits[$criterion['id']] ?? 1; 
            }
            $minMax[$criterion['id']] = $criterion['type'] == 'benefit' ? max($values) : min($values);
        }

        $results = [];
        foreach ($gaps as $gap) {
            $totalScore = 0;
            $normalizedScores = [];
            
            foreach ($snapshotCriteria as $criterion) {
                $x = $evaluations[$gap->id][$criterion['id']] ?? 1; 
                $weight = $criterion['weight']; 
                $r = $criterion['type'] == 'benefit' ? ($x / $minMax[$criterion['id']]) : ($minMax[$criterion['id']] / $x);
                $normalizedScores[$criterion['id']] = $r;
                $totalScore += ($r * $weight);
            }
            $results[] = ['gap' => $gap, 'original' => $evaluations[$gap->id], 'normalized' => $normalizedScores, 'final_score' => $totalScore];
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
            $currentMaturity = 5; $targetLevel = null; $recommendation = '';
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

        $avg = AuditResponse::where('audit_id', $audit->id)->avg('score');
        $audit->itml_score = $avg ? round($avg * 5, 2) : 0;

        $progressNotes = AuditProgress::where('audit_id', $audit->id)->pluck('notes', 'domain_name')->toArray();

        return view('manager.result', compact('audit', 'results', 'criteria', 'roadmaps', 'progressNotes'));
    }


    public function storeProgress(Request $request, Audit $audit)
    {
        $request->validate([
            'domain_name' => 'required|string',
            'notes' => 'required|string'
        ]);

        AuditProgress::updateOrCreate(
            ['audit_id' => $audit->id, 'domain_name' => $request->domain_name],
            ['notes' => $request->notes]
        );

        return redirect()->back()->with('success', 'Catatan progress untuk ' . explode(' - ', $request->domain_name)[0] . ' berhasil diperbarui!');
    }
}