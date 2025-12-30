<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    APP Reminder Billing
  </title>
  <!-- Fonts and icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px; /* Mengecilkan ukuran */
        height: 35px; /* Mengecilkan ukuran */
        border: none !important;
        background: transparent !important;
        color: #17c1e8 !important;
        padding: 0;
        border-radius: 8px;
        margin: 0 0.2em;
        transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #e0e0e0 !important;
        color: #17c1e8 !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        transform: scale(1.05);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #17c1e8 !important;
        color: white !important;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next {
        background: transparent;
        color: #17c1e8 !important;
        font-size: 1.2rem;
        padding: 0 0.6rem;
        border-radius: 8px;
        transition: background-color 0.3s ease, transform 0.2s;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.previous:hover,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next:hover {
        background-color: #e0e0e0 !important;
        color: #17c1e8 !important;
        box-shadow: none;
        transform: scale(1.05);
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1.5rem;
        text-align: center;
        margin-right: 1rem;
    }

    .dataTables_length, .dataTables_info {
        margin-bottom: 1.5rem;
        margin-top: 1.5rem;
        margin-left: 24px;
    }

    .dataTables_info {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .dataTables_empty {
        text-align: center !important;
        font-size: 0.75rem !important;
        color: #999;
    }

    .dataTables_filter {
        margin-top: 1.5rem;
        margin-right: 24px;
    }

</style>


</head>

<body class="g-sidenav-show bg-gray-100">

  @include('layouts.navbars.auth.sidebar')

  <div class="main-content">
      
      @include('layouts.navbars.auth.nav')

      <div class="container-fluid py-4">
          @yield('content')
      </div>
  </div>

  <!-- SweetAlert Delete -->
  <script>
      function confirmDelete(id) {
          Swal.fire({
              title: 'Apakah kamu yakin ingin menghapus data ini?',
              text: "Data tidak dapat dikembalikan!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Hapus'
          }).then((result) => {
              if (result.isConfirmed) {
                  document.getElementById('delete-form-' + id).submit();
              }
          });
      }
  </script>

  <!-- SweetAlert Success -->
  @if (session('success'))
      <script>
          Swal.fire({
              icon: 'success',
              title: 'Success',
              text: '{{ session('success') }}',
              showConfirmButton: false,
              timer: 2000
          });
      </script>
  @endif

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/fullcalendar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
  @stack('rtl')
  @stack('dashboard')

  <!-- jQuery and DataTables JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

  <script>
    $(document).ready(function() {
        $('.table').DataTable({
            "paging": true,
            "ordering": true, 
            "info": true,          
            "searching": true,    
            "language": {
                "paginate": {
                    "previous": "‹",
                    "next": "›"
                },
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "lengthMenu": "Show _MENU_ entries"
            },
            "lengthMenu": [5, 10, 25, 50],
            "dom": 'lfrtip',
            "initComplete": function() {
                $('.table').removeClass('dataTable');
            }
        });
    });
  </script>

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>
</body>
</html>