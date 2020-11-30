<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if(isset($title)){{ $title }} | @endif{{ config('app.name', 'Hotel Manager') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>

    <!-- Styles -->
    {!! Html::style('css/app.min.css') !!}
    {!! Html::style('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css') !!}
    {!! Html::style('css/fixes.min.css') !!}
    @yield('css')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Hotel Manager') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @auth
                            <!-- Rooms -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @lang('general.rooms') <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('room.index') }}">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.show_rooms')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('room.free') }}">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.currently_free_rooms')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('room.occupied') }}">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.currently_occupied_rooms')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('room.addform') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_room')
                                    </a>
                                </div>
                            </li>

                            <!-- Guests -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @lang('general.guests') <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('guest.index') }}">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.show_guests')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('guest.addform') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_guest')
                                    </a>
                                </div>
                            </li>

                            <!-- Reservations -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @lang('general.reservations') <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('reservation.index') }}">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.all_reservations')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('reservation.current') }}">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.current_reservations')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('reservation.future') }}">
                                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.future_reservations')
                                    </a>
                                    <a class="dropdown-item" href="{{ route('reservation.addform') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_reservation')
                                    </a>
                                </div>
                            </li>

                            @if (Auth::user()->isAdmin())
                                <!-- Users -->
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        @lang('general.users') <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('user.index') }}">
                                            <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.show_users')
                                        </a>
                                        <a class="dropdown-item" href="{{ route('user.addform') }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_user')
                                        </a>
                                    </div>
                                </li>

                                <!-- Admin -->
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.index') }}">
                                        @lang('general.administration_panel')
                                    </a>
                                </li>
                            @endauth
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Languages -->
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fa fa-globe" aria-hidden="true"></i> @lang('general.language') <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('lang.set', 'en') }}">
                                    {{ Html::image('img/en.png', 'English') }} English
                                </a>
                                <a class="dropdown-item" href="{{ route('lang.set', 'pl')}}">
                                    {{ Html::image('img/pl.png', 'Polski') }} Polski
                                </a>
                            </div>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">@lang('auth.login')</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">@lang('auth.register')</a>
                            </li> --}}
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @lang('navigation.hello') {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fa fa-power-off" aria-hidden="true"></i>
                                        @lang('auth.logout')
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    {!! Html::script('/js/app.min.js') !!}
    {!! Html::script('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js') !!}
    {!! Html::script('/js/datepickersettings.min.js') !!}
    @yield('js')
</body>
</html>
