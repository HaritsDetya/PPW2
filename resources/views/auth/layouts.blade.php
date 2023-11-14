<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container">
            <a href="{{URL('/')}}" class="navbar-brand">Custom Login Register</a>
            <button class="navbar-toggler" type="button" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Togle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    @guest
                    <li class="nav-item">
                        <a href="{{route('login')}}" class="nav-link {{(request()->is('login')) ? 'active':''}}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('register')}}" class="nav-link {{(request()->is('register')) ? 'active':''}}">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->is('users')) ? 'active' : '' }}" href="{{ route('users') }}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->is('gallery')) ? 'active' : '' }}" href="{{route('gallery.index') }}">Gallery</a>
                    </li>
                    @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>

                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>