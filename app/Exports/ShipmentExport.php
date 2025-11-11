<?php

namespace App\Exports;

use App\Models\Shipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ShipmentExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Shipment::query();

        if (!empty($this->filters['date_from']) && !empty($this->filters['date_to'])) {
            $query->whereBetween('delivery_date', [$this->filters['date_from'], $this->filters['date_to']]);
        }

        if (!empty($this->filters['route'])) {
            $query->where('route', $this->filters['route']);
        }

        if (!empty($this->filters['gate'])) {
            $query->where('gate', $this->filters['gate']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->get([
            'noshipment',
            'delivery_date',
            'route',
            'gate',
            'status'
        ]);
    }

    public function headings(): array
    {
        return ['No Shipment', 'Delivery Date', 'Route', 'Gate', 'Status'];
    }
}
