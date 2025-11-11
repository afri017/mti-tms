<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return view('vendors.index', compact('vendors'))->with('pageTitle', 'List Vendor');
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'transporter_name' => 'required',
            'notelp' => 'required',
            'address' => 'required',
            'npwp' => 'required',
        ]);

        Vendor::create([
            'idvendor' => 'TR' . str_pad(Vendor::count() + 1, 3, '0', STR_PAD_LEFT),
            'transporter_name' => $request->transporter_name,
            'notelp' => $request->notelp,
            'address' => $request->address,
            'npwp' => $request->npwp,
            'created_by' => auth()->user()->name ?? 'system',
        ]);

        return redirect()->route('vendors.index')->with('success', 'Transporter berhasil ditambahkan.');
    }

    public function edit($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, $idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);

        $request->validate([
            'transporter_name' => 'required',
            'notelp' => 'required',
            'address' => 'required',
            'npwp' => 'required',
        ]);

        $vendor->update([
            'transporter_name' => $request->transporter_name,
            'notelp' => $request->notelp,
            'address' => $request->address,
            'npwp' => $request->npwp,
            'updated_by' => auth()->user()->name ?? 'system',
        ]);

        return redirect()->route('vendors.index')->with('success', 'Transporter berhasil diperbarui.');
    }

    public function destroy($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Transporter berhasil dihapus.');
    }
}
