<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class OutstandingPOExport implements FromCollection, WithHeadings
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * Headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'PO Number',
            'Customer',
            'PO Date',
            'Material',
            'Qty Order (Ton)',
            'Qty Delivered (Ton)',
            'Qty Outstanding (Ton)',
            'UOM'
        ];
    }

    /**
     * Build collection for export
     */
    public function collection()
    {
        $start = $this->start;
        $end   = $this->end;

        // Ambil semua PO dengan relasi item & customer dalam rentang tanggal
        $pos = PurchaseOrder::with(['poItems', 'customer'])
            ->whereBetween('podate', [$start, $end])
            ->get();

        // Ambil total qty yang sudah digunakan (delivery order) dan konversi ke ton
        $usedQty = DB::table('delivery_order_items as doi')
            ->join('delivery_orders as do', 'doi.nodo', '=', 'do.nodo')
            ->select(
                'do.nopo',
                'doi.material_code',
                'doi.uom',
                DB::raw('SUM(doi.qty_plan) as qty_used')
            )
            ->groupBy('do.nopo', 'doi.material_code', 'doi.uom')
            ->get()
            ->mapWithKeys(function ($row) {
                $key = $row->nopo . '_' . $row->material_code;

                $qtyInTon = match (strtolower($row->uom)) {
                    'kg'  => $row->qty_used / 1000,
                    'g'   => $row->qty_used / 1_000_000,
                    'ton' => $row->qty_used,
                    default => $row->qty_used,
                };

                return [$key => $qtyInTon];
            });

        $rows = [];

        foreach ($pos as $po) {

            $poNumber = $po->nopo;
            $custName = optional($po->customer)->customer_name;
            $poDate   = $po->podate;

            foreach ($po->poItems as $item) {
                $key = $poNumber . '_' . $item->material_code;
                $usedInTon = $usedQty[$key] ?? 0;

                $qtyOrder = (float) $item->qty;
                $qtyDelivered = (float) $usedInTon;
                $qtyOutstanding = max(0, $qtyOrder - $qtyDelivered);

                // hanya export jika ada outstanding (jika ingin semua item, hapus kondisi ini)
                // jika Anda ingin semua item termasuk yg 0 outstanding, hapus if berikut
                if ($qtyOutstanding <= 0) {
                    continue;
                }

                $rows[] = [
                    $poNumber,
                    $custName,
                    $poDate,
                    $item->material_code,
                    $qtyOrder,
                    $qtyDelivered,
                    $qtyOutstanding,
                    $item->uom,
                ];
            }
        }

        return new Collection($rows);
    }
}
