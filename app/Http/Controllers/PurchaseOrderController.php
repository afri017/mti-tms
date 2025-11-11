<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\POItem;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Customer;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $pageTitle = 'Purchase Order';
        $breadchumb = 'Purchase Order';

        // Ambil semua PO beserta item dan customer
        $pos = PurchaseOrder::with('poItems', 'customer')->get();

        // Ambil semua qty yang sudah digunakan per kombinasi nopo + material_code
        $usedQty = \DB::table('delivery_order_items as doi')
            ->join('delivery_orders as do', 'doi.nodo', '=', 'do.nodo')
            ->select('do.nopo', 'doi.material_code', 'doi.uom', \DB::raw('SUM(doi.qty_plan) as qty_used'))
            ->groupBy('do.nopo', 'doi.material_code', 'doi.uom')
            ->get()
            ->mapWithKeys(function($row){
                // Buat key gabungan nopo_material_code
                $key = $row->nopo . '_' . $row->material_code;

                // Konversi qty ke ton
                $qtyInTon = match(strtolower($row->uom)) {
                    'kg' => $row->qty_used / 1000,
                    'g'  => $row->qty_used / 1_000_000,
                    'ton' => $row->qty_used,
                    default => $row->qty_used, // jika UOM lain, pakai apa adanya
                };

                return [$key => $qtyInTon];
            });

            // Hitung status PO dan still to be delivered per item
            foreach ($pos as $po) {

                $poTotalQty = 0; // total qty PO
                $poTotalStillToDeliver = 0; // total still to be delivered

                $allItemsDelivered = true;
                $anyItemDelivered = false;

                foreach ($po->poItems as $item) {
                    $key = $po->nopo . '_' . $item->material_code;
                    $usedInTon = $usedQty[$key] ?? 0;

                    // qty sisa untuk item
                    $item->still_to_be_delivered = max(0, $item->qty - $usedInTon);

                    $poTotalQty += $item->qty;
                    $poTotalStillToDeliver += $item->still_to_be_delivered;

                    if ($item->still_to_be_delivered > 0) {
                        $allItemsDelivered = false;
                    }

                    if ($item->still_to_be_delivered < $item->qty) {
                        $anyItemDelivered = true;
                    }
                }

                // Tentukan status PO berdasarkan item
                if ($allItemsDelivered) {
                    $po->status = 'Complete';
                } elseif ($anyItemDelivered) {
                    $po->status = 'Partial Delivery';
                } else {
                    $po->status = 'Open';
                }

                // Simpan summary untuk header
                $po->po_total_qty = $poTotalQty;
                $po->po_total_still_to_deliver = $poTotalStillToDeliver;
            }

        return view('po.index', compact('pos', 'pageTitle','breadchumb'));
    }

    public function create()
    {
        $pageTitle = 'Create Purchase Order';
        $customers = Customer::all();
        $materials = Material::all();

        return view('po.create', compact('pageTitle', 'customers', 'materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idcustomer' => 'required',
            'podate' => 'required|date',
            'valid_to' => 'required|date',
            'items.*.material_code' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uom' => 'required'
        ]);

        DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'idcustomer' => $request->idcustomer,
                'podate' => $request->podate,
                'valid_to' => $request->valid_to,
                'created_by' => auth()->user()->name ?? 'system'
            ]);

            $no = 10;
            foreach ($request->items as $item) {
                POItem::create([
                    'nopo' => $po->nopo,
                    'itempo' => $no,
                    'material_code' => $item['material_code'],
                    'qty' => $item['qty'],
                    'uom' => $item['uom'],
                    'created_by' => auth()->user()->name ?? 'system'
                ]);
                $no += 10;
            }
        });

        return redirect()->route('po.index')->with('success', 'PO berhasil dibuat');
    }

    public function edit(PurchaseOrder $po)
    {
        $po->load('poItems'); // gunakan nama relasi yang benar
        $customers = Customer::all();
        $materials = Material::all();
        return view('po.edit', compact('po','customers','materials'))->with('pageTitle', 'Edit PO');
    }

    public function update(Request $request, PurchaseOrder $po)
    {
        $request->validate([
            'idcustomer' => 'required',
            'podate' => 'required|date',
            'valid_to' => 'required|date',
            'items.*.material_code' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.uom' => 'required'
        ]);

        DB::transaction(function() use ($request, $po) {
            $po->update([
                'idcustomer' => $request->idcustomer,
                'podate' => $request->podate,
                'valid_to' => $request->valid_to,
            ]);

            // Hapus POItem lama
            POItem::where('nopo', $po->nopo)->delete();

            // Simpan POItem baru
            $no = 10;
            foreach($request->items as $item){
                POItem::create([
                    'nopo' => $po->nopo,
                    'itempo' => $no,
                    'material_code' => $item['material_code'],
                    'qty' => $item['qty'],
                    'uom' => $item['uom'],
                    'created_by' => auth()->user()->name ?? 'system'
                ]);
                $no += 10;
            }
        });

        return redirect()->route('po.index')->with('success','PO berhasil diperbarui');
    }

    public function destroy(PurchaseOrder $po)
    {
        DB::transaction(function() use ($po){
            POItem::where('nopo', $po->nopo)->delete();
            $po->delete();
        });

        return redirect()->route('po.index')->with('success','PO berhasil dihapus');
    }
}
