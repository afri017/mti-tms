<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar material.
     */
    public function index()
    {
        $materials = Material::orderBy('id', 'desc')->get();
        $pageTitle = 'Material Master';
        $breadchumb = 'Table Maintenance';
        return view('product.index', compact('materials','pageTitle','breadchumb'));
    }

    /**
     * Form tambah material baru.
     */
    public function create()
    {
        $pageTitle = 'Material Master';
        $breadchumb = 'Table Maintenance';
        return view('product.create',compact('pageTitle','breadchumb'));
    }

    /**
     * Simpan material baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'material_desc' => 'required|string|max:255',
            'uom' => 'required|string|max:10',
            'konversi_ton' => 'nullable|numeric',
        ]);

        $material = new Material([
            'material_desc' => $request->material_desc,
            'uom' => $request->uom,
            'konversi_ton' => $request->konversi_ton ?? 0,
            'created_by' => Auth::user()->name ?? 'system',
            'update_by' => Auth::user()->name ?? 'system',
            'last_update' => now(),
        ]);

        $material->save();

        return redirect()->route('product.index')
                         ->with('success', 'Material berhasil ditambahkan.');
    }

    /**
     * Form edit material.
     */
    public function edit($id)
    {
        $material = Material::findOrFail($id);
        $pageTitle = 'Material Master';
        $breadchumb = 'Table Maintenance';
        return view('product.edit', compact('material','pageTitle','breadchumb'));
    }

    /**
     * Update material.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'material_desc' => 'required|string|max:255',
            'uom' => 'required|string|max:10',
            'konversi_ton' => 'nullable|numeric',
        ]);

        $material = Material::findOrFail($id);
        $material->update([
            'material_desc' => $request->material_desc,
            'uom' => $request->uom,
            'konversi_ton' => $request->konversi_ton ?? 0,
            'update_by' => Auth::user()->name ?? 'system',
            'last_update' => now(),
        ]);

        return redirect()->route('product.index')
                         ->with('success', 'Material berhasil diperbarui.');
    }

    /**
     * Hapus material.
     */
    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return redirect()->route('product.index')
                         ->with('success', 'Material berhasil dihapus.');
    }
}
