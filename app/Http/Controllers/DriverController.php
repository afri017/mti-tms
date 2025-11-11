<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return view('drivers.index', compact('drivers'))->with('pageTitle', 'List Drivers');
    }

    public function create()
    {
        return view('drivers.create')->with('pageTitle', 'Add Driver');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'no_sim' => 'required|string|max:50|unique:drivers',
            'typesim' => 'required|string|max:5',
            'notelp' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        // ID akan otomatis dibuat di model (boot method)
        Driver::create($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return view('drivers.edit', compact('driver'))->with('pageTitle', 'Edit Driver');
    }

    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'no_sim' => 'required|string|max:50|unique:drivers,no_sim,' . $driver->iddriver . ',iddriver',
            'typesim' => 'required|string|max:5',
            'notelp' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver berhasil dihapus.');
    }
}
