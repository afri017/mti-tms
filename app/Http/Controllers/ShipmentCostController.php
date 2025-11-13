<?php

namespace App\Http\Controllers;

use App\Models\ShipmentCost;
use App\Models\Vendor;
use App\Models\Route;
use App\Models\Tonnage;
use App\Models\Gate;
use App\Models\GateUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShipmentCostController extends Controller
{
    public function index()
    {
        $shipmentCosts = ShipmentCost::with(['vendor', 'routeData', 'truckType'])
        ->orderByDesc('created_at')
        ->paginate(10);
        return view('shipment_cost.index', compact('shipmentCosts'));

    }

    public function create()
    {
        $vendors = Vendor::all();
        $routes = Route::all();
        $tonnages = Tonnage::all();
        return view('shipment_cost.create', compact('vendors', 'routes', 'tonnages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idvendor' => 'required',
            'route' => 'required',
            'type_truck' => 'required',
            'price_freight' => 'required|numeric',
            'price_driver' => 'required|numeric',
            'validity_start' => 'required|date',
            'validity_end' => 'required|date|after_or_equal:validity_start',
            'active' => 'nullable|in:Y,',
        ]);

        // Handle checkbox: convert empty string to null
        $data = $request->all();
        $data['active'] = $request->input('active') === 'Y' ? 'Y' : null;

        ShipmentCost::create($data);

        return redirect()->route('shipment_cost.index')->with('success', 'Shipment Cost berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $shipmentCost = ShipmentCost::findOrFail($id);
        $vendors = Vendor::all();
        $routes = Route::all();
        $tonnages = Tonnage::all();
        return view('shipment_cost.edit', compact('shipmentCost', 'vendors', 'routes', 'tonnages'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'idvendor' => 'required',
            'route' => 'required',
            'type_truck' => 'required',
            'price_freight' => 'required|numeric',
            'price_driver' => 'required|numeric',
            'validity_start' => 'required|date',
            'validity_end' => 'required|date|after_or_equal:validity_start',
            'active' => 'nullable|in:Y,',
        ]);

        // Handle checkbox: convert empty string to null
        $data = $request->all();
        $data['active'] = $request->input('active') === 'Y' ? 'Y' : null;

        $shipmentCost = ShipmentCost::findOrFail($id);
        $shipmentCost->update($data);

        return redirect()->route('shipment_cost.index')->with('success', 'Shipment Cost berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $shipmentCost = ShipmentCost::findOrFail($id);
        $shipmentCost->delete();
        return redirect()->route('shipment_cost.index')->with('success', 'Shipment Cost berhasil dihapus.');
    }
}
