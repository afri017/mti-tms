<table class="table table-bordered table-sm mt-2">
    <thead class="bg-secondary text-white">
        <tr>
            <th>No Shipment</th>
            <th>Gate-In</th>
            <th>Gate-Out</th>
            <th>Start Loading</th>
            <th>End Loading</th>
            <th>Receipt</th>
            <th>Tara</th>
            <th>Gross</th>
            <th>Qty Plan</th>
            <th>Qty Receipt</th>
            <th>Qty Actual</th>
            <th>Reject</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($detail as $d)
            <tr>
                <td>{{ $d->noshipment }}</td>
                <td>{{ $d->checkin ?? '-' }}</td>
                <td>{{ $d->checkout ?? '-' }}</td>
                <td>{{ $d->start_loading ?? '-' }}</td>
                <td>{{ $d->end_loading ?? '-' }}</td>
                <td>{{ $d->receipt_date ?? '-' }}</td>
                <td>{{ $d->tara_weight }}</td>
                <td>{{ $d->gross_weight }}</td>
                <td>{{ $d->total_qty }}</td>
                <td>{{ $d->total_receipt }}</td>
                <td>{{ $d->total_act }}</td>
                <td>{{ $d->total_reject }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center text-muted">No Data</td>
            </tr>
        @endforelse
    </tbody>
</table>
