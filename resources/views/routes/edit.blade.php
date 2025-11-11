@extends('layout.main')
@section('content')
<section class="content">
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Edit Route</h3>
  </div>

  <div class="card-body">
    <form action="{{ route('routes.update', $route->route) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Source</label>
            <select name="source" class="form-control" required>
                @foreach($sources as $s)
                    <option value="{{ $s->id }}" {{ $route->source == $s->id ? 'selected' : '' }}>
                        {{ $s->location_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Destination</label>
            <select name="destination" class="form-control" required>
                @foreach($destinations as $d)
                    <option value="{{ $d->id }}" {{ $route->destination == $d->id ? 'selected' : '' }}>
                        {{ $d->location_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Leadtime (hari)</label>
            <input type="number" name="leadtime" value="{{ $route->leadtime }}" class="form-control" min="0" required>
        </div>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('routes.index') }}" class="btn btn-secondary">Kembali</a>
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
