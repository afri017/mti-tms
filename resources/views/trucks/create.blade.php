@extends('layout.main')
@section('content')
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">New Truck</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
    <form action="{{ route('trucks.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="idvendor">Vendor</label>
            <select name="idvendor" id="idvendor" class="form-control" required>
                <option value="">-- Pilih Vendor --</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->idvendor }}"
                        {{ old('idvendor', $truck->idvendor ?? '') == $vendor->idvendor ? 'selected' : '' }}>
                        {{ $vendor->transporter_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Driver</label>
            <select name="iddriver" class="form-control" required>
                <option value="">-- Pilih Driver --</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->iddriver }}">{{ $driver->iddriver }} - {{ $driver->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Tipe Truck</label>
            <select name="type_truck" class="form-control" required>
                <option value="">-- Pilih Tonnage --</option>
                @foreach($tonnages as $t)
                    <option value="{{ $t->id }}">{{ $t->id }} - {{ $t->desc }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>STNK</label>
            <input type="text" name="stnk" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Merk</label>
            <input type="text" name="merk" class="form-control" required>
        </div>
        <div class="form-group">
            <label>No Polisi</label>
            <input type="text" name="nopol" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Expired KIR</label>
            <input type="date" name="expired_kir" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('trucks.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
        <!-- /.row -->
    </div>
    <!-- /.card-body -->
</section>
<!-- /.content -->
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
