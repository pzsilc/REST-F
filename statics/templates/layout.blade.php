<!<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ $app_name }} {{ isset($title) ? "- $title" : '' }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">    
    <link mhref="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" mrel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&family=Yanone+Kaffeesatz:wght@500&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='{{ $app_path }}/statics/css/app.css'/>
</head>

<body id="page-top">
        <header>
            <nav class="navbar navbar-expand">
                <a class="navbar-brand magenta" href="{{ $app_path }}/">Ankieta</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class='fa fa-bars'></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        @if(isset($_SESSION['user']))
                            @if($_SESSION['user']->is_admin)
                                <li class="nav-item active">
                                    <a class="nav-link text-muted" href="{{ $app_path }}/dashboard">Panel administratora</a>
                                </li>
                            @endif
                            <li class="nav-item active">
                                <a class="nav-link text-muted" href="{{ $app_path }}/auth/logout">Wyloguj się</a>
                            </li>
                        @endif
                    </ul>                   
                </div>
            </nav>
            @include('messages.index')
        </header>
        <main class="mt-5">
            @yield('main')
        </main>
        <footer>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#dd1177" fill-opacity="1" d="M0,128L60,122.7C120,117,240,107,360,133.3C480,160,600,224,720,213.3C840,203,960,117,1080,101.3C1200,85,1320,139,1380,165.3L1440,192L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
                <g>
                    <text x="50" y="250" font-family="Arial" font-size="17" font-weight="bold"  fill="white">Copyright &copy; Silcare {{ date('Y') }}</text>
                </g>    
            </svg>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="{{ $app_path }}/statics/js/app.js"></script>
</body>

</html>