<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>MTI | {{ $pageTitle ?? 'Transport Management System' }}</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="{{ asset('dist/img/loading-bar.png') }}" alt="Logo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
              <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" role="button"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
              <i class="fas fa-th-large"></i>
            </a>
        </li> -->
    </ul>
    {{-- Hidden logout form --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
      <img src="{{ asset('dist/img/logo-ipc-1.png') }}" alt="Admin" class="brand-image " style="opacity: .8"> <!-- src="../dist/img/logo-ipc-1.png"  {{ asset('dist/img/Admin.png') }}-->
      <span class="brand-text font-weight-light">MTI</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('dist/img/' . Auth::user()->gambar) }}" class="img-circle elevation-3" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <!-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar text-sm" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent text-sm" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-header">MAIN MENU</li>
          <li class="nav-item ">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <!-- <i class="right fas fa-angle-left"></i> -->
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link {{ request()->routeIs('permissions.*') || request()->routeIs('roles.*') || request()->routeIs('users.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
                Settings
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="right badge badge-danger">New</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item ">
                  <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>PERMISSION</p>
                  </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ROLE</p>
                    </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>USER</p>
                  </a>
                </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link {{ request()->routeIs('gates.*') || request()->routeIs('shipment_cost.*') || request()->routeIs('vendors.*') || request()->routeIs('customer.*') || request()->routeIs('routes.*') || request()->routeIs('product.*') || request()->routeIs('sources.*') || request()->routeIs('trucks.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-book-open"></i>
              <p>
                Master Data
                <i class="fas fa-angle-left right"></i>
                <!-- <span class="badge badge-info right">6</span> -->
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('gates.index') }}" class="nav-link {{ request()->routeIs('gates.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>GATE</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('customer.index') }}" class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>CUSTOMER</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('routes.index') }}" class="nav-link {{ request()->routeIs('routes.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>ROUTE</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('tonnages.index') }}" class="nav-link {{ request()->routeIs('tonnages.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TONNAGE</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('trucks.index') }}" class="nav-link {{ request()->routeIs('trucks.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TRUCK</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('drivers.index') }}" class="nav-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>DRIVER</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('vendors.index') }}" class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TRANSPORTER</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('sources.index') }}" class="nav-link {{ request()->routeIs('sources.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SOURCE</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('shipment_cost.index') }}" class="nav-link {{ request()->routeIs('shipment_cost.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SHIP COST</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('product.index') }}" class="nav-link {{ request()->routeIs('product.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PRODUCT</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link {{ request()->routeIs('po.*') || request()->routeIs('shipment.*') || request()->routeIs('optimization') || request()->routeIs('delivery_scheduling.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-truck"></i>
              <p>
                Bookings
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('po.index') }}" class="nav-link {{ request()->routeIs('po.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PURCHASE</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('shipment.index') }}" class="nav-link {{ request()->routeIs('shipment.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TRUCK ASSIGNMENT</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('optimization') }}" class="nav-link {{ request()->routeIs('optimization') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>BUILD TRUCK LOAD</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('delivery_scheduling.index') }}" class="nav-link {{ request()->routeIs('delivery_scheduling.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>DELIVERY SCHEDULLING</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link {{ request()->routeIs('do.edit') || request()->routeIs('do.receipt') ? 'active' : '' }}">
              <i class="nav-icon fas fa-road"></i>
              <p>
                Shipment
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('do.edit') }}" class="nav-link {{ request()->routeIs('do.edit') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>REMOVAL</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('do.receipt') }}" class="nav-link {{ request()->routeIs('do.receipt') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RECEIPT</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('do.index.check') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>REALIZATION</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-ticket-alt"></i>
              <p>
                Ticket
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="dashboard.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>DASHBOARD</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="queuing.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>GATE QUEUING</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link {{ request()->routeIs('tracking.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-map-marker-alt"></i>
              <p>
                Tracking
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('tracking.index') }}" class="nav-link {{ request()->routeIs('tracking.index') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>LOCATION</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('tracking.history') }}" class="nav-link {{ request()->routeIs('tracking.history') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>MOVEMENT HYSTORY</p>
                  </a>
              </li>
          </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Reports
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pocust.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>OUTSTANDING PO CUSTOMER</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="gatemonitor.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>GATE MONITORING</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="truckmonitor.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TRUCK MONITORING</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="truckprod.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>TRUCK PRODUKTIVITY</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="lostload.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>LOSS LOAD</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
              <form method="POST" action="{{ route('logout') }}" class="nav-link">
                  @csrf
                  <button type="submit" class="nav-link btn btn-link p-0 m-0 text-start" style="display: flex; align-items: center;">
                      <i class="nav-icon fas fa-sign-out-alt"></i>
                      <p class="mb-0 ms-2">Logout</p>
                  </button>
              </form>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4 class="m-0">{{ $breadchumb ?? '' }}</h4>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
              <li class="breadcrumb-item active">{{ $pageTitle ?? '' }}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->

    @yield('content')


    <a id="back-to-top" href="#" class="btn btn-secondary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
      </a>
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2026 <a href="https://mtimultiscm.co.id/">MTI</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0.0
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>

<!-- DataTables & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<!-- PAGE PLUGINS -->
<script src="{{ asset('plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo -->
<script src="{{ asset('dist/js/pages/dashboard2.js') }}"></script>

{{--  Tempat untuk menaruh script tambahan dari halaman lain --}}
    @stack('scripts')


</body>
</html>
