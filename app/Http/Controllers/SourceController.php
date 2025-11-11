<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class SourceController extends Controller
{
    public function index()
    {
        $sources = Source::all();
        return view('sources.index', compact('sources'));
    }

    public function create()
    {
        return view('sources.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:Source,Destination',
            'location_name' => 'required|string|max:255|unique:sources,location_name,NULL,id,type,' . $request->type,
            'capacity' => 'nullable|numeric|min:0'
        ], [
            'type.required' => 'Tipe harus diisi (Source/Destination).',
            'location_name.required' => 'Nama lokasi harus diisi.',
            'location_name.unique' => 'Kombinasi tipe dan lokasi sudah ada.',
            'capacity.numeric' => 'Kapasitas harus berupa angka.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $id = Source::generateId($request->type);

            Source::create([
                'id' => $id,
                'type' => $request->type,
                'location_name' => $request->location_name,
                'capacity' => $request->capacity ?? 0,
                'created_by' => auth()->user()->name ?? 'system',
                'update_by' => auth()->user()->name ?? 'system',
                'last_update' => now(),
            ]);

            return redirect()->route('sources.index')->with('success', 'Data source berhasil ditambahkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(Source $source)
    {
        return view('sources.edit', compact('source'));
    }

    public function update(Request $request, Source $source)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:Source,Destination',
            'location_name' => 'required|string|max:255|unique:sources,location_name,' . $source->id . ',id,type,' . $request->type,
            'capacity' => 'nullable|numeric|min:0'
        ], [
            'type.required' => 'Tipe harus diisi (Source/Destination).',
            'location_name.required' => 'Nama lokasi harus diisi.',
            'location_name.unique' => 'Kombinasi tipe dan lokasi sudah ada.',
            'capacity.numeric' => 'Kapasitas harus berupa angka.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $source->update([
                'type' => $request->type,
                'location_name' => $request->location_name,
                'capacity' => $request->capacity ?? 0,
                'update_by' => auth()->user()->name ?? 'system',
                'last_update' => now(),
            ]);

            return redirect()->route('sources.index')->with('success', 'Data source berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(Source $source)
    {
        try {
            $source->delete();
            return redirect()->route('sources.index')->with('success', 'Data source berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
