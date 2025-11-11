<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Jalan {{ $do->nodo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 2px; text-align: center; }
        h2 { text-align: center; margin-bottom: 10px; }
        .header, .footer { width: 100%; }
        .footer td { border: none; text-align: center; padding-top: 40px; }
        .no-border td { border: none; }
        .terms {
            font-size: 10px;
            margin-top: 30px;
            border: 1px solid #333;
            padding: 8px;
        }
        .logo {
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    {{-- HEADER DENGAN LOGO --}}
    <table class="no-border">
        <tr>
            <td style="width: 10%; text-align:left;">
                <img src="{{ public_path('dist/img/logo-ipc-1.png') }}" class="logo" alt="Company Logo">
            </td>
        </tr>
        <tr>
            <td style="width: 70%; text-align:left; vertical-align: top;">
            <Strong>Kepada : </Strong> {{ $do->poheader?->customer?->customer_name }} <br> {{ $do->poheader?->customer?->address }}

            </td>
            <td style="width: 100%; text-align:center;">
                <h2>SURAT JALAN</h2>
            </td>
            <td style="width: 70%; text-align:left; vertical-align: top;">
            <Strong>Source : </Strong> <br> {{ $do->sourceLocation?->location_name }} - {{ $do->destinationLocation?->location_name }}
            </td>
        </tr>
    </table>

    {{-- INFORMASI UTAMA --}}
    <table class="header">
        <tr>
            <td style="border: 0; text-align: left; width: 22%;"><strong>No. Shipment</strong></td>
            <td style="border: 0; width: 2%;">:</td>
            <td style="border: 0; text-align: left; width: 50%;">{{ $shipment->noshipment }}</td>
            <td style="border: 0; text-align: left; width: 15%;"><strong>No. DO</strong></td>
            <td style="border: 0; width: 2%;">:</td>
            <td style="border: 0; text-align: left; width: 33%;">{{ $do->nodo }}</td>
        </tr>
        <tr>
            <td style="border: 0; text-align: left; width: 22%;"><strong>No. Polisi</strong></td>
            <td style="border: 0; width: 2%;">:</td>
            <td style="border: 0; text-align: left; width: 50%;">{{ $nopol }}</td>
            <td style="border: 0; text-align: left; width: 15%;"><strong>Driver</strong></td>
            <td style="border: 0; width: 2%;">:</td>
            <td style="border: 0; text-align: left; width: 33%;">{{ $driver }}</td>
        </tr>
        <tr>
            <td style="border: 0; text-align: left; width: 22%;"><strong>Tanggal</strong></td>
            <td style="border: 0; width: 2%;">:</td>
            <td style="border: 0; text-align: left; width: 50%;">{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</td>
            <td style="border: 0; text-align: left; width: 15%;"><strong>No. Seal</strong></td>
            <td style="border: 0; width: 2%;">:</td>
            <td style="border: 0; text-align: left; width: 33%;">{{ $shipment->noseal }}</td>
        </tr>
    </table>

    {{-- DETAIL ITEM --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Material</th>
                <th>Deskripsi</th>
                <th>Qty Actual</th>
                <th>UOM</th>
            </tr>
        </thead>
        <tbody>
            @foreach($do->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->material_code }}</td>
                <td>{{ $item?->material?->material_desc }}</td>
                <td>{{ number_format($item->qty_act ?? 0, 2) }}</td>
                <td>{{ $item->uom }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- KETERANGAN --}}
    <p><strong>Keterangan:</strong> {{ $remarks }}</p>

    {{-- TANDA TANGAN --}}
    <table class="footer">
        <tr>
            <td><strong>Driver</strong><br><br><br>(_________________)</td>
            <td><strong>Security</strong><br><br><br>(_________________)</td>
            <td><strong>Gudang</strong><br><br><br>(_________________)</td>
            <td><strong>Penerima</strong><br><br><br>(_________________)</td>
        </tr>
    </table>

    {{-- TERM & CONDITION --}}
    <div class="terms">
        <strong>Terms & Conditions:</strong>
        <ol>
            <li>Barang yang telah diterima dalam kondisi baik tidak dapat dikembalikan tanpa persetujuan pihak pengirim.</li>
            <li>Surat jalan ini wajib dibawa selama perjalanan dan ditunjukkan kepada pihak yang berwenang bila diminta.</li>
            <li>Segala kerusakan atau kehilangan selama pengiriman menjadi tanggung jawab transporter sesuai perjanjian.</li>
            <li>Pastikan tanda tangan penerima sesuai dan disertai cap perusahaan (bila ada).</li>
        </ol>
    </div>
</body>
</html>
