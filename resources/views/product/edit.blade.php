@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Edit Material</h3>
  </div>

  <div class="card-body">
    <form action="{{ route('product.update', $material->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card-body">
          <div class="form-group">
              <label>Kode Material</label>
              <input type="text" class="form-control" value="{{ $material->material_code }}" readonly>
          </div>

          <div class="form-group">
              <label>Deskripsi Material</label>
              <input type="text" name="material_desc" class="form-control" value="{{ $material->material_desc }}" required>
          </div>

          <div class="form-group">
              <label>Satuan (UOM)</label>
              <input type="text" name="uom" class="form-control" value="{{ $material->uom }}" required>
          </div>

          <div class="form-group">
              <label>Konversi ke Ton</label>
              <input type="number" step="0.01" name="konversi_ton" class="form-control" value="{{ $material->konversi_ton }}" required>
          </div>

          <div class="form-group">
              <label>Diperbarui Oleh</label>
              <input type="text" name="update_by" class="form-control" value="{{ $material->update_by }}">
          </div>
      </div>

      <div class="card-footer text-right">
          <a href="{{ route('product.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-warning">
              <i class="fas fa-save"></i> Update
          </button>
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
