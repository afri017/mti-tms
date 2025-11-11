@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Edit Customer</h3>
  </div>

  <div class="card-body">
    <form action="{{ route('customer.update', $customer->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="idcustomer">ID Customer</label>
        <input type="text" name="idcustomer" id="idcustomer" class="form-control"
               value="{{ old('idcustomer', $customer->idcustomer) }}" required>
      </div>

      <div class="form-group">
        <label for="customer_name">Nama Customer</label>
        <input type="text" name="customer_name" id="customer_name" class="form-control"
               value="{{ old('customer_name', $customer->customer_name) }}" required>
      </div>

      <div class="form-group">
        <label for="address">Alamat</label>
        <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $customer->address) }}</textarea>
      </div>

      <div class="form-group">
        <label for="notelp">No. Telepon</label>
        <input type="text" name="notelp" id="notelp" class="form-control"
               value="{{ old('notelp', $customer->notelp) }}">
      </div>

      <div class="form-group">
        <label for="is_active">Status</label>
        <select name="is_active" id="is_active" class="form-control">
          <option value="1" {{ $customer->is_active == 1 ? 'selected' : '' }}>Aktif</option>
          <option value="0" {{ $customer->is_active == 0 ? 'selected' : '' }}>Non-Aktif</option>
        </select>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('customer.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
</section>
@endsection

@push('scripts')
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    })

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })

  })
</script>
@endpush
