<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIXID</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB UI Kit -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.min.css" rel="stylesheet" />
    <!-- Favicon -->
    <link rel="shortcut icon"
        href="https://media.licdn.com/dms/image/v2/C510BAQGcQObmC5ADzw/company-logo_200_200/company-logo_200_200/0/1630582740922/tix_id_logo?e=2147483647&v=beta&t=y7u2Lvw4QGrKw64ZYzcBTp34x8Ih-c1-dy_-m9NV4W4">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">


    <style>
        :root {
            --primary-color: #108EE9;
            --primary-hover: #0d7bc7;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        /* .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        } */

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(16, 142, 233, 0.25);
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container-fluid">

            <!-- Brand -->
            <a class="navbar-brand fs-3 fw-bold me-4" href="{{route('home')}}">TIX ID</a>

            <!-- Mobile toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if (Auth::check() && Auth::user()->role == 'admin')
                    <li>
                        <a class="nav-link" href="{{route('admin.dashboard')}}">Dashboard</a>
                    </li>
                        <li class="nav-item dropdown">
                            <a data-mdb-dropdown-init class="nav-link dropdown-toggle" href="#"
                                id="navbarDropdownMenuLink" role="button" aria-expanded="false">
                                Data Master
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.cinemas.index') }}">Bioskop</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.movies.index') }}">Film</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">Petugas</a>
                                </li>
                            </ul>
                        </li>
                    @elseif (Auth::check() && Auth::user()->role == 'staff')
                        <li class="nav-item">
                            <a href="{{ route('staff.promos.index') }}" class="nav-link">Promo</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('staff.schedule.index')}}" class="nav-link">Jadwal Tayang</a>
                        </li>
                    @else
                        <!-- Navigation links - Left side -->

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 px-3" href="{{ route('home') }}">
                                <i class="fas fa-home"></i>
                                <span>Beranda</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 px-3" href="{{route('cinemas.list')}}">
                                <i class="fas fa-film"></i>
                                <span>Bioskop</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 px-3" href="{{route('tickets.index')}}">
                                <i class="fas fa-ticket-alt"></i>
                                <span>Tiket</span>
                            </a>
                        </li>
                    @endif
                </ul>


                <div class="d-flex justify-content-center flex-grow-1 mx-3">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="search" class="form-control border-end-0"
                            placeholder="Cari film, bioskop, atau lokasi..." aria-label="Search">
                        <button class="btn btn-outline-secondary border-start-0 bg-white" type="button">
                            <i class="fas fa-search text-muted"></i>
                        </button>
                    </div>
                </div>

                @if (Auth::check())
                    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                @else
                    <div class="d-flex gap-2 ms-3">
                        <a href="{{ route('login') }}" class="btn text-primary">Login</a>
                        <a href="{{ route('signup') }}" class="btn btn-primary">Sign-Up</a>
                    </div>
                @endif

            </div>
        </div>
    </nav>

    <!-- Content -->
    @yield('content')

    <footer class="bg-body-tertiary text-center text-lg-start mt-5">
        <div class="text-center p-3" style="background-color: #ffffff">
            © 2025 Copyright:
            <a href="" class="text-body"> TixId</a>
        </div>
    </footer>
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"
        integrity="sha512-2rNj2KJ+D8s1ceNasTIex6z4HWyOnEYLVC3FigGOmyQCZc2eBXKgOxQmo3oKLHyfcj53uz4QMsRCWNbLd32Q1g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.umd.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('script')
</body>
</html>
