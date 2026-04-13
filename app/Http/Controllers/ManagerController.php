<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\Criterion;
use App\Models\AuditResponse;
use App\Models\GapEvaluation; 
use App\Models\Domain; 

class ManagerController extends Controller
{

    public function dashboard()
    {
        $audits = Audit::where('status', 'completed')
                       ->orderBy('updated_at', 'desc')
                       ->get();

        $domains = Domain::all();
        $chartLabels = [];
        $chartData = [];

        foreach ($domains as $domain) {
            $chartLabels[] = $domain->code;

            $averageScore = AuditResponse::whereHas('audit', function ($q) {
                                $q->where('status', 'completed');
                            })
                            ->join('cobit_questions', 'audit_responses.cobit_question_id', '=', 'cobit_questions.id')
                            ->where('cobit_questions.domain_id', $domain->id)
                            ->avg('audit_responses.score');

            $chartData[] = $averageScore ? round($averageScore * 5, 2) : 0;
        }

        // Lempar variabel chartLabels dan chartData ke halaman web
        return view('manager.dashboard', compact('audits', 'chartLabels', 'chartData'));
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

    // ========================================================
    // FUNGSI 1: SIMPAN NILAI + SNAPSHOT
    // ========================================================
    public function calculate(Request $request, Audit $audit)
    {
        $request->validate(['evaluations' => 'required|array']);

        // Ambil kriteria master yang aktif SAAT INI
        $criteria = Criterion::all()->keyBy('id');

        foreach ($request->evaluations as $gapId => $crits) {
            foreach ($crits as $criterionId => $score) {
                $master = $criteria[$criterionId];
                
                // Simpan nilai inputan, DAN kunci bobotnya selamanya!
                GapEvaluation::updateOrCreate(
                    ['audit_response_id' => $gapId, 'criterion_id' => $criterionId],
                    [
                        'score' => $score,
                        'weight_snapshot' => $master->weight,
                        'type_snapshot' => $master->type
                    ]
                );
            }
        }

        return redirect()->route('manager.audit.result', $audit->id);
    }
    
    // ========================================================
    // FUNGSI 2: TAMPILKAN HASIL MENGGUNAKAN DATA BEKU (SNAPSHOT)
    // ========================================================
    public function showResult(Audit $audit)
    {
        // Tarik data gap beserta hasil evaluasi masa lalu (walau kriterianya udah dihapus)
        $gaps = AuditResponse::with(['question.domain', 'gapEvaluations.criterion'])
                    ->where('audit_id', $audit->id)
                    ->where('score', '<', 1)
                    ->get();

        $evaluations = [];
        $snapshotCriteria = []; 

        foreach ($gaps as $gap) {
            foreach ($gap->gapEvaluations as $eval) {
                $evaluations[$gap->id][$eval->criterion_id] = $eval->score;

                // Bangun kriteria dari data snapshot fotokopian!
                if (!isset($snapshotCriteria[$eval->criterion_id])) {
                    $snapshotCriteria[$eval->criterion_id] = [
                        'id' => $eval->criterion_id,
                        // Pakai nilai fotokopian. Kalau datanya data lama banget (belum ada snapshot), fallback ke master
                        'weight' => $eval->weight_snapshot ?? ($eval->criterion->weight ?? 0),
                        'type' => $eval->type_snapshot ?? ($eval->criterion->type ?? 'benefit'),
                        'name' => $eval->criterion->name ?? 'Kriteria (Telah Dihapus)'
                    ];
                }
            }
        }

        if (empty($evaluations)) {
            return redirect()->route('manager.audit.evaluate', $audit->id);
        }

        // --- MULAI PERHITUNGAN ---
        $minMax = [];
        foreach ($snapshotCriteria as $criterion) {
            $values = [];
            foreach ($evaluations as $gapId => $crits) {
                $values[] = $crits[$criterion['id']] ?? 1; // Fallback 1 jika kosong
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
            $results[] = [
                'gap' => $gap, 'original' => $evaluations[$gap->id],
                'normalized' => $normalizedScores, 'final_score' => $totalScore
            ];
        }
        
        // Urutkan Ranking SAW
        usort($results, function($a, $b) { return $b['final_score'] <=> $a['final_score']; });

        // --- LOGIKA MAPPING SKOR SAW UNTUK ROADMAP ---
        $domainSawScores = [];
        foreach ($results as $res) {
            $domainName = $res['gap']->question->domain->code . ' - ' . $res['gap']->question->domain->name;
            if (!isset($domainSawScores[$domainName])) {
                $domainSawScores[$domainName] = $res['final_score'];
            } else {
                $domainSawScores[$domainName] = max($domainSawScores[$domainName], $res['final_score']);
            }
        }

        // --- LOGIKA ROADMAP ---
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
        
        // Sort Roadmap ikuti SAW
        usort($roadmaps, function($a, $b) { return $b['sort_score'] <=> $a['sort_score']; });

        $criteria = $snapshotCriteria; 

        return view('manager.result', compact('audit', 'results', 'criteria', 'roadmaps'));
    }
}