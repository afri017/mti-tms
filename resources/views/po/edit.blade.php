@extends('layout.main')
@section('content')
<section class="content">
<div class="container-fluid">
    <div class="card card-default">
        <div class="card-header">
            <h4 class="card-title">{{ $pageTitle }}</h4>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('po.update', $po->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Customer -->
                <div class="form-group">
                    <label>Customer</label>
                    <select name="idcustomer" class="form-control" required>
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->idcustomer }}" @if($po->idcustomer == $c->idcustomer) selected @endif>
                                {{ $c->customer_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal PO -->
                <div class="form-row">
                    <div class="col">
                        <label>Tanggal PO</label>
                        <input type="date" name="podate" class="form-control" value="{{ $po->podate }}" required>
                    </div>
                    <div class="col">
                        <label>Berlaku Sampai</label>
                        <input type="date" name="valid_to" class="form-control" value="{{ $po->valid_to }}" required>
                    </div>
                </div>

                <hr>
                <h5>Item PO</h5>
                <table class="table table-bordered" id="po-item-table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Qty</th>
                            <th>UOM</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $itemIndex = 0; @endphp
                        @foreach($po->poItems ?? [] as $item)
                        <tr>
                            <td>
                                <select name="items[{{ $itemIndex }}][material_code]" class="form-control" required>
                                    <option value="">-- Pilih Material --</option>
                                    @foreach($materials as $m)
                                        <option value="{{ $m->material_code }}" @if($item->material_code == $m->material_code) selected @endif>
                                            {{ $m->material_desc }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[{{ $itemIndex }}][qty]" class="form-control" value="{{ $item->qty }}" min="1" required></td>
                            <td><input type="text" name="items[{{ $itemIndex }}][uom]" class="form-control" value="{{ $item->uom }}" required></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        @php $itemIndex++; @endphp
                        @endforeach
                    </tbody>
                </table>

                <button type="button" class="btn btn-secondary btn-sm" id="add-item">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>

                <hr>
                <button type="submit" class="btn btn-success">Update PO</button>
                <a href="{{ route('po.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
</section>

<script>
let itemIndex = {{ $itemIndex }}; // sesuai jumlah item awal

// Tambah item baru
document.getElementById('add-item').addEventListener('click', function() {
    const table = document.querySelector('#po-item-table tbody');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td>
            <select name="items[${itemIndex}][material_code]" class="form-control" required>
                <option value="">-- Pilih Material --</option>
                @foreach($materials as $m)
                    <option value="{{ $m->material_code }}">{{ $m->material_desc }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="items[${itemIndex}][qty]" class="form-control" min="1" required></td>
        <td><input type="text" name="items[${itemIndex}][uom]" class="form-control" required></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
    `;
    table.appendChild(row);
    itemIndex++;
});

// Hapus item
document.addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
        e.target.closest('tr').remove();
    }
});
</script>
@endsection
