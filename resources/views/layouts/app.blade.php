<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if(isset($title)){{ $title }} | @endif{{ config('app.name', 'Hotel Manager') }}</title>

    <!-- Fonts -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>

    <!-- Styles -->
    {!! Html::style('css/app.min.css') !!}
    {!! Html::style('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css') !!}
    {!! Html::style('css/fixes.min.css') !!}
    @yield('css')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Hotel Manager') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    @auth
                        <ul class="nav navbar-nav">
                            <!-- Rooms -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">@lang('general.rooms') <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('room.index') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.show_rooms')</a></li>
                                    <li><a href="{{ route('room.free') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.currently_free_rooms')</a></li>
                                    <li><a href="{{ route('room.occupied') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.currently_occupied_rooms')</a></li>
                                    <li><a href="{{ route('room.addform') }}"><i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_room')</a></li>
                                </ul>
                            </li>

                            <!-- Guests -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">@lang('general.guests') <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('guest.index') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.show_guests')</a></li>
                                    <li><a href="{{ route('guest.addform') }}"><i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_guest')</a></li>
                                </ul>
                            </li>

                            <!-- Reservations -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">@lang('general.reservations') <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('reservation.index') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.all_reservations')</a></li>
                                    <li><a href="{{ route('reservation.current') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.current_reservations')</a></li>
                                    <li><a href="{{ route('reservation.future') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.future_reservations')</a></li>
                                    <li><a href="{{ route('reservation.addform') }}"><i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_reservation')</a></li>
                                </ul>
                            </li>

                            @if (Auth::user()->isAdmin())
                                <!-- Users -->
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">@lang('general.users') <span class="caret"></span></a>

                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('user.index') }}"><i class="fa fa-list-ul" aria-hidden="true"></i> @lang('navigation.show_users')</a></li>
                                        <li><a href="{{ route('user.addform') }}"><i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_user')</a></li>
                                    </ul>
                                </li>

                                <!-- Admin -->
                                <li><a href="{{ route('admin.index') }}">@lang('general.administration_panel')</a></li>
                            @endauth
                        </ul>
                    @endauth

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Languages -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-globe" aria-hidden="true"></i> @lang('general.language') <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ route('lang.set', 'en') }}">{{ Html::image('img/en.png', 'English') }} English</a></li>
                                <li><a href="{{ route('lang.set', 'pl')}}">{{ Html::image('img/pl.png', 'Polski') }} Polski</a></li>
                            </ul>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">@lang('auth.login')</a></li>
                            {{-- <li><a href="{{ route('register') }}">@lang('auth.register')</a></li> --}}
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                    @lang('navigation.hello') {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('user.change_password') }}"><i class="fa fa-key" aria-hidden="true"></i> @lang('auth.change_password')</a></li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="fa fa-power-off" aria-hidden="true"></i>
                                            @lang('auth.logout')
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    {!! Html::script('/js/app.min.js') !!}
    {!! Html::script('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js') !!}
    {!! Html::script('/js/datepickersettings.min.js') !!}
    @yield('js')
</body>
</html>
