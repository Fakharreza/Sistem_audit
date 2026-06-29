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

    public function updateAll(Request $request)
    {
        $request->validate([
            'criteria' => 'nullable|array',
            'criteria.*.name' => 'required_with:criteria|string|max:255',
            'criteria.*.type' => 'required_with:criteria|in:benefit,cost',
            'criteria.*.weight' => 'required_with:criteria|numeric|min:0.01|max:1',
            
            'new_criteria' => 'nullable|array',
            'new_criteria.*.name' => 'required_with:new_criteria|string|max:255',
            'new_criteria.*.type' => 'required_with:new_criteria|in:benefit,cost',
            'new_criteria.*.weight' => 'required_with:new_criteria|numeric|min:0.01|max:1',
        ]);

        $totalWeight = 0;
        if ($request->has('criteria')) {
            foreach ($request->criteria as $data) {
                $totalWeight += (float) $data['weight'];
            }
        }
        if ($request->has('new_criteria')) {
            foreach ($request->new_criteria as $data) {
                $totalWeight += (float) $data['weight'];
            }
        }

        if (round($totalWeight, 2) != 1.00) {
            return redirect()->back()->with('error', 'Gagal menyimpan! Total akumulasi bobot harus tepat 1.00.');
        }

        if ($request->has('criteria')) {
            foreach ($request->criteria as $id => $data) {
                $criterion = Criterion::find($id);
                if ($criterion) {
                    $criterion->update($data);
                }
            }
        }

        if ($request->has('new_criteria')) {
            foreach ($request->new_criteria as $newData) {
                Criterion::create($newData);
            }
        }

        return redirect()->back()->with('success', 'Semua perubahan dan penambahan kriteria berhasil disimpan!');
    }

    public function destroy(Criterion $criterion)
    {
        $criterion->delete();
        return redirect()->back()->with('success', 'Kriteria berhasil dihapus!');
    }

    public function reset()
    {
        Criterion::query()->delete();

        Criterion::create(['name' => 'Urgensi', 'type' => 'benefit', 'weight' => 0.50]);
        Criterion::create(['name' => 'Biaya', 'type' => 'cost', 'weight' => 0.30]);
        Criterion::create(['name' => 'Kemudahan', 'type' => 'benefit', 'weight' => 0.20]);

        return redirect()->back()->with('success', 'Kriteria berhasil dikembalikan ke Setelan Default!');
    }
}