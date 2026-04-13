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
                            // Join ke tabel pertanyaan untuk ngecek ini domain apa
                            ->join('cobit_questions', 'audit_responses.cobit_question_id', '=', 'cobit_questions.id')
                            ->where('cobit_questions.domain_id', $domain->id)
                            ->avg('audit_responses.score');

            $chartData[] = $averageScore ? round($averageScore * 5, 2) : 0;
        }

        // Lempar variabel chartLabels dan chartData ke halaman web
        return view('manager.dashboard', compact('audits', 'chartLabels', 'chartData'));
    }

    // 2. Menampilkan Form Input Nilai SAW (Hanya untuk temuan/gap)
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

    // 3. Menyimpan Inputan Manajer ke Database
    public function calculate(Request $request, Audit $audit)
    {
        $request->validate([
            'evaluations' => 'required|array'
        ]);

        // Simpan setiap nilai kriteria ke tabel gap_evaluations
        foreach ($request->evaluations as $gapId => $crits) {
            foreach ($crits as $criterionId => $score) {
                GapEvaluation::updateOrCreate(
                    ['audit_response_id' => $gapId, 'criterion_id' => $criterionId],
                    ['score' => $score]
                );
            }
        }

        // Setelah disimpan, langsung arahkan ke halaman hasil
        return redirect()->route('manager.audit.result', $audit->id);
    }
    
    // 4. Mengambil Data dari Database & Menghitung Ulang Rumus SAW
    public function showResult(Audit $audit)
    {
        $criteria = Criterion::all();

        // Ambil aktivitas gap beserta nilai evaluasi yang tadi sudah disimpan
        $gaps = AuditResponse::with(['question.domain', 'gapEvaluations'])
                    ->where('audit_id', $audit->id)
                    ->where('score', '<', 1)
                    ->get();

        // Bangun ulang array evaluations dari database
        $evaluations = [];
        foreach ($gaps as $gap) {
            foreach ($gap->gapEvaluations as $eval) {
                $evaluations[$gap->id][$eval->criterion_id] = $eval->score;
            }
        }

        // Cek jika belum ada data evaluasi di database
        if (empty($evaluations)) {
            return redirect()->route('manager.audit.evaluate', $audit->id);
        }

        // --- MULAI PERHITUNGAN MATEMATIKA SAW ---
        
        // Tahap 1: Cari Nilai Min/Max
        $minMax = [];
        foreach ($criteria as $criterion) {
            $values = [];
            foreach ($evaluations as $gapId => $crits) {
                $values[] = $crits[$criterion->id];
            }
            
            if ($criterion->type == 'benefit') {
                $minMax[$criterion->id] = max($values);
            } else {
                $minMax[$criterion->id] = min($values);
            }
        }

        // Tahap 2 & 3: Normalisasi dan Perhitungan V
        $results = [];
        foreach ($gaps as $gap) {
            $totalScore = 0;
            $normalizedScores = [];

            foreach ($criteria as $criterion) {
                $x = $evaluations[$gap->id][$criterion->id]; 
                $weight = $criterion->weight; 

                if ($criterion->type == 'benefit') {
                    $r = $x / $minMax[$criterion->id];
                } else {
                    $r = $minMax[$criterion->id] / $x;
                }

                $normalizedScores[$criterion->id] = $r;
                $totalScore += ($r * $weight);
            }

            $results[] = [
                'gap' => $gap,
                'original' => $evaluations[$gap->id],
                'normalized' => $normalizedScores,
                'final_score' => $totalScore
            ];
        }

        // Tahap 4: Perangkingan
        usort($results, function($a, $b) {
            return $b['final_score'] <=> $a['final_score'];
        });

        // Lempar ke halaman result.blade.php
        return view('manager.result', compact('audit', 'results', 'criteria'));
    }
}