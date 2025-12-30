<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                    @if (request()->is('/')) 
                        Dashboard 
                    @else
                        {{ str_replace('-', ' ', Request::path()) }}
                    @endif
                </li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">
                @if (request()->is('/')) 
                    Dashboard 
                @else
                    {{ str_replace('-', ' ', Request::path()) }}
                @endif
            </h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar"> 
            <div class="ms-md-3 pe-md-3 d-flex align-items-center">
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-flex align-items-center me-3">
                        <span id="nav-wa-status" class="badge bg-gradient-secondary shadow-sm">
                            <i class="fas fa-plug me-1"></i> Checking...
                        </span>
                    </li>

                    <li class="nav-item d-xl-none ps-3 me-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a href="{{ url('/logout')}}" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none">Log Out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

{{-- Script Socket untuk Navbar --}}
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    const navStatus = document.getElementById('nav-wa-status');
    
    // Sesuaikan alamat IP & Port dengan server Node.js Sir
    const navSocket = io("{{ config('services.whatsapp.node_endpoint') }}");

    navSocket.on('connect', () => {
        // Saat socket terhubung, status default adalah mengecek WA login
        updateNavStatus('Checking WA...', 'bg-gradient-info', 'fa-spinner fa-spin');
    });

    navSocket.on('status', (status) => {
        if (status === 'Connected') {
            updateNavStatus('WA Connected', 'bg-gradient-success', 'fa-check-circle');
        } else if (status === 'Waiting for Scan' || status === 'Authenticated') {
            updateNavStatus('WA Needs Action', 'bg-gradient-warning', 'fa-exclamation-triangle');
        } else {
            updateNavStatus('WA Logged Out', 'bg-gradient-danger', 'fa-times-circle');
        }
    });

    navSocket.on('disconnect', () => {
        updateNavStatus('Server Node Offline', 'bg-gradient-danger', 'fa-power-off');
    });

    function updateNavStatus(text, bgColor, icon) {
        navStatus.className = `badge ${bgColor} shadow-sm`;
        navStatus.innerHTML = `<i class="fas ${icon} me-1"></i> ${text}`;
    }
</script>