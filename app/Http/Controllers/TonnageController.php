<?php

namespace App\Http\Controllers;

use App\Models\Tonnage;
use Illuminate\Http\Request;

class TonnageController extends Controller
{
    public function index()
    {
        $tonnages = Tonnage::all();
        return view('tonnages.index', compact('tonnages'))->with('pageTitle', 'List Tonnages');
    }

    public function create()
    {
        return view('tonnages.create')->with('pageTitle', 'Craete Tonnage');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_truck' => 'required|integer',
            'desc' => 'required|string|max:255',
        ]);

        Tonnage::create($validated);

        return redirect()->route('tonnages.index')->with('success', 'Tonnase berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tonnage = Tonnage::findOrFail($id);
        return view('tonnages.edit', compact('tonnage'))->with('pageTitle', 'Edit Tonnage');
    }

    public function update(Request $request, $id)
    {
        $tonnage = Tonnage::findOrFail($id);

        $validated = $request->validate([
            'type_truck' => 'required|integer',
            'desc' => 'required|string|max:255',
        ]);

        $tonnage->update($validated);

        return redirect()->route('tonnages.index')->with('success', 'Tonnase berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tonnage = Tonnage::findOrFail($id);
        $tonnage->delete();
        return redirect()->route('tonnages.index')->with('success', 'Tonnase berhasil dihapus.');
    }
}
