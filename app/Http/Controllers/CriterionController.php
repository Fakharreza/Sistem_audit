<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criterion;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::all();
        $totalWeight = $criteria->sum('weight'); 
        
        return view('manager.criteria.index', compact('criteria', 'totalWeight'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0.01|max:1',
        ]);

        Criterion::create($request->all());
        return redirect()->back()->with('success', 'Kriteria berhasil ditambahkan!');
    }

   // 3. Update BANYAK kriteria sekaligus (Bulk Update)
    public function updateAll(Request $request)
    {
        // Validasi inputan array
        $request->validate([
            'criteria' => 'required|array',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.type' => 'required|in:benefit,cost',
            'criteria.*.weight' => 'required|numeric|min:0.01|max:1',
        ]);

        // Looping untuk nyimpen masing-masing perubahan
        foreach ($request->criteria as $id => $data) {
            $criterion = Criterion::find($id);
            if ($criterion) {
                $criterion->update($data);
            }
        }

        return redirect()->back()->with('success', 'Semua perubahan kriteria berhasil disimpan!');
    }

    public function destroy(Criterion $criterion)
    {
        $criterion->delete();
        return redirect()->back()->with('success', 'Kriteria berhasil dihapus!');
    }
    public function reset()
    {
        
        Criterion::query()->delete();

        Criterion::create([
            'name' => 'Urgensi',
            'type' => 'benefit',
            'weight' => 0.50
        ]);
        
        Criterion::create([
            'name' => 'Biaya',
            'type' => 'cost',
            'weight' => 0.30
        ]);

        Criterion::create([
            'name' => 'Kemudahan',
            'type' => 'benefit',
            'weight' => 0.20
        ]);

        return redirect()->back()->with('success', 'Kriteria berhasil dikembalikan ke Setelan Default!');
    }
}