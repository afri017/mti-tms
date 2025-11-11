@extends('layout.main')
@section('content')
<section class="content">
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <a href="{{ route('po.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah PO</a>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
            </div>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th></th> <!-- tombol expand -->
                    <th>No</th>
                    <th>No PO</th>
                    <th>Tanggal PO</th>
                    <th>Customer</th>
                    <th>Valid To</th>
                    <th>Status</th>
                    <th>Total Qty PO</th>
                    <th>Still to be delivered</th>
                    <th>Created By</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pos as $po)
                <tr>
                    <td>
                        <button class="btn btn-sm btn-info toggle-items" data-target="#items-{{ $po->id }}">
                            <i class="fas fa-plus"></i>
                        </button>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $po->nopo }}</td>
                    <td>{{ $po->podate }}</td>
                    <td>{{ $po->customer->customer_name ?? '-' }}</td>
                    <td>{{ $po->valid_to }}</td>
                    <td>{{ $po->status }}</td>
                    <td>{{ $po->po_total_qty }} (M/T)</td>
                    <td>{{ $po->po_total_still_to_deliver }} (M/T)</td>
                    <td>{{ $po->created_by }}</td>
                    <td>
                        <a href="{{ route('po.edit', $po->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>

                        <form action="{{ route('po.destroy', $po->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin hapus PO ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
                <tr id="items-{{ $po->id }}" class="collapse">
                    <td colspan="8">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>Qty</th>
                                    <th>Still to be delivered</th>
                                    <th>UOM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->poItems as $item)
                                <tr>
                                    <td>{{ $item->itempo }}</td>
                                    <td>{{ $item->material_code }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ $item->still_to_be_delivered }}</td>
                                    <td>{{ $item->uom }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.toggle-items').forEach(button => {
    button.addEventListener('click', function() {
        const target = document.querySelector(this.dataset.target);
        target.classList.toggle('show');

        // toggle icon plus/minus
        const icon = this.querySelector('i');
        if(target.classList.contains('show')){
            icon.classList.remove('fa-plus');
            icon.classList.add('fa-minus');
        } else {
            icon.classList.remove('fa-minus');
            icon.classList.add('fa-plus');
        }
    });
});
</script>
@endsection



