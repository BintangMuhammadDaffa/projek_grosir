<?php

namespace App\Http\Controllers;

use App\Models\OperationalCost;
use Illuminate\Http\Request;

class OperationalCostController extends Controller
{
    public function index()
    {
        if (!auth()->user()->canAccessOperationalCosts()) {
            abort(403, 'Unauthorized access');
        }

        $costs = OperationalCost::with('user')
            ->orderBy('cost_date', 'desc')
            ->paginate(15);

        return view('operational-costs.index', compact('costs'));
    }

    public function create()
    {
        if (!auth()->user()->canAccessOperationalCosts()) {
            abort(403, 'Unauthorized access');
        }

        return view('operational-costs.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canAccessOperationalCosts()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'cost_code' => 'nullable|unique:operational_costs',
            'description' => 'required',
            'category' => 'required|in:rent,utilities,salary,marketing,maintenance,other',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date'
        ]);

        OperationalCost::create([
            'cost_code' => $request->cost_code,
            'description' => $request->description,
            'category' => $request->category,
            'amount' => $request->amount,
            'cost_date' => $request->cost_date,
            'user_id' => auth()->id(),
            'notes' => $request->notes
        ]);

        return redirect()->route('operational-costs.index')->with('success', 'Biaya operasional berhasil ditambahkan');
    }

    public function show(OperationalCost $operational_cost)
    {
        if (!$operational_cost->exists) {
            abort(404);
        }

        if (!auth()->user()->canAccessOperationalCosts()) {
            abort(403, 'Unauthorized access');
        }

        return view('operational-costs.show', [
        'cost' => $operational_cost
    ]);
    }

    public function edit(OperationalCost $operational_cost)
    {
        if (!$operational_cost->exists) {
            abort(404);
        }

        if (!auth()->user()->canAccessOperationalCosts()) {
            abort(403, 'Unauthorized access');
        }

        return view('operational-costs.edit', [
        'cost' => $operational_cost
    ]);
    }

    public function update(Request $request, OperationalCost $operational_cost)
    {
        if (!$operational_cost->exists) {
            abort(404);
        }

        if (!auth()->user()->canAccessOperationalCosts()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'cost_code' => 'required|unique:operational_costs,cost_code,'.$operational_cost->id,
            'description' => 'required',
            'category' => 'required|in:rent,utilities,salary,marketing,maintenance,other',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date'
        ]);

        $operational_cost->update([
            'cost_code' => $request->cost_code,
            'description' => $request->description,
            'category' => $request->category,
            'amount' => $request->amount,
            'cost_date' => $request->cost_date,
            'notes' => $request->notes
        ]);

        return redirect()->route('operational-costs.index')->with('success', 'Biaya operasional berhasil diperbarui');
    }

    public function destroy(OperationalCost $operational_cost)
    {
        if (!$operational_cost->exists) {
            abort(404);
        }

        if (!auth()->user()->canAccessOperationalCosts()) {
            abort(403, 'Unauthorized access');
        }

        $operational_cost->delete();

        return redirect()->route('operational-costs.index')->with('success', 'Biaya operasional berhasil dihapus');
    }
}
