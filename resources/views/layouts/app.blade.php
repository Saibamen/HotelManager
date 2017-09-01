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
    {!! Html::style('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css') !!}

    <!-- Styles -->
    {!! Html::style('css/app.min.css') !!}
    @yield('css')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
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
                                    <li><a href="{{ route('room.index') }}"><i class="fa fa-list-alt" aria-hidden="true"></i> @lang('navigation.show_rooms')</a></li>
                                    <li><a href="{{ route('room.addform') }}"><i class="fa fa-plus" aria-hidden="true"></i> @lang('navigation.add_room')</a></li>
                                    <li><a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> !@lang('navigation.currently_free_rooms')</a></li>
                                    <li><a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> !@lang('navigation.currently_occupied_rooms')</a></li>
                                </ul>
                            </li>

                            <!-- Guests -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">@lang('general.guests') <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    <li><a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> !@lang('navigation.show_guests')</a></li>
                                    <li><a href="#"><i class="fa fa-plus" aria-hidden="true"></i> !@lang('navigation.add_guest')</a></li>
                                </ul>
                            </li>

                            <!-- Reservations -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">@lang('general.reservations') <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    <li><a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> !@lang('navigation.all_reservations')</a></li>
                                    <li><a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> !@lang('navigation.current_reservations')</a></li>
                                    <li><a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> !@lang('navigation.future_reservations')</a></li>
                                    <li><a href="#"><i class="fa fa-plus" aria-hidden="true"></i> !@lang('navigation.add_reservation')</a></li>
                                </ul>
                            </li>
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
                            <li><a href="{{ route('register') }}">@lang('auth.register')</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
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
    @yield('js')
</body>
</html>
