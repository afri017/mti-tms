<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Tonnage;
use App\Models\Vendor;

class TruckController extends Controller
{
    public function index()
    {
        $trucks = Truck::with(['driver', 'tonnage'])->get();
        return view('trucks.index', compact('trucks'))->with('pageTitle', 'List Truck');
    }

    public function create()
    {
        $drivers = Driver::all();
        $tonnages = Tonnage::all();
        $vendors = Vendor::all();
        return view('trucks.create', compact('drivers', 'tonnages', 'vendors'))->with('pageTitle', 'Add Truck');
    }

    public function store(Request $request)
    {
        $request->validate([
            'idvendor' => 'required|exists:vendors,idvendor',
            'iddriver' => 'required|exists:drivers,iddriver',
            'type_truck' => 'required|exists:tonnages,id',
            'stnk' => 'required',
            'merk' => 'required',
            'nopol' => 'required',
            'expired_kir' => 'required|date',
        ]);

        Truck::create([
            'idvendor' => $request->idvendor,
            'iddriver' => $request->iddriver,
            'type_truck' => $request->type_truck,
            'stnk' => $request->stnk,
            'merk' => $request->merk,
            'nopol' => $request->nopol,
            'expired_kir' => $request->expired_kir,
            'created_by' => auth()->user()->name ?? 'system',
        ]);

        return redirect()->route('trucks.index')->with('success', 'Data Truck berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $truck = Truck::findOrFail($id);
        $drivers = Driver::all();
        $tonnages = Tonnage::all();
        $vendors = Vendor::all();
        return view('trucks.edit', compact('truck', 'drivers', 'tonnages', 'vendors'))->with('pageTitle', 'Edit Truck');
    }

    public function update(Request $request, $id)
    {
        $truck = Truck::findOrFail($id);

        $request->validate([
            'idvendor' => 'required|exists:vendors,idvendor',
            'iddriver' => 'required|exists:drivers,iddriver',
            'type_truck' => 'required|exists:tonnages,id',
            'stnk' => 'required',
            'merk' => 'required',
            'nopol' => 'required',
            'expired_kir' => 'required|date',
        ]);

        $truck->update([
            'idvendor' => $request->idvendor,
            'iddriver' => $request->iddriver,
            'type_truck' => $request->type_truck,
            'stnk' => $request->stnk,
            'merk' => $request->merk,
            'nopol' => $request->nopol,
            'expired_kir' => $request->expired_kir,
            'update_by' => auth()->user()->name ?? 'system',
        ]);

        return redirect()->route('trucks.index')->with('success', 'Data Truck berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $truck = Truck::findOrFail($id);
        $truck->delete();
        return redirect()->route('trucks.index')->with('success', 'Data Truck berhasil dihapus.');
    }
}
