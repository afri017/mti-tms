<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use App\Models\Source;
use Illuminate\Http\Request;

class GateController extends Controller
{
    public function index()
    {
        $gates = Gate::with('source')->get();
        return view('gates.index', compact('gates'))->with('pageTitle', 'List Gate');
    }

    public function create()
    {
        $sources = Source::all();
        return view('gates.create', compact('sources'))->with('pageTitle', 'Add Gate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'gate' => 'required|string|max:10',
            'point' => 'required|exists:sources,id',
            'timestart' => 'required',
            'timeend' => 'required',
            'type' => 'required|integer',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        Gate::create([
            'gate' => $request->gate,
            'point' => $request->point,
            'timestart' => $request->timestart,
            'timeend' => $request->timeend,
            'type' => $request->type,
            'duration_minutes' => $request->duration_minutes,
        ]);

        return redirect()->route('gates.index')->with('success', 'Data Gate berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $gate = Gate::findOrFail($id);
        $sources = Source::all();
        return view('gates.edit', compact('gate', 'sources'))->with('pageTitle', 'Edit Gate');
    }

    public function update(Request $request, $id)
    {
        $gate = Gate::findOrFail($id);

        $request->validate([
            'gate' => 'required|string|max:10',
            'point' => 'required|exists:sources,id',
            'timestart' => 'required',
            'timeend' => 'required',
            'type' => 'required|integer',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $gate->update([
            'gate' => $request->gate,
            'point' => $request->point,
            'timestart' => $request->timestart,
            'timeend' => $request->timeend,
            'type' => $request->type,
            'duration_minutes' => $request->duration_minutes,
        ]);

        return redirect()->route('gates.index')->with('success', 'Data Gate berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $gate = Gate::findOrFail($id);
        $gate->delete();
        return redirect()->route('gates.index')->with('success', 'Data Gate berhasil dihapus.');
    }
}
