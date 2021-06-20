<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('luna.title', 'Luna') }}</title>
    <link href="/luna/css/index.css" rel="stylesheet">
    @routes('luna')
    <script defer>
        window.user = JSON.parse('@json(auth()->user())');

        @if(auth()->check())
            window.luna = @json(Luna::export());
            window.lang = @json(__('luna'));
        @endif
    </script>
    <script src="/luna/js/index.js" defer></script>
</head>
<body class="rtl">
<div id="app" style="display: none">
    <nav class="navbar navbar-dark bg-dark navbar-expand-md fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ ucfirst(config('app.name')) }}
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-navbar-collapse"
                    aria-controls="app-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item mr-0">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <span>خروج</span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                              style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div id="app-loading" v-if="isLoading">
            <div class="load-bar">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <nav class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-sticky">
                    <div class="user d-flex align-items-start">
                        <div class="avatar">
                            <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?s=40"
                                 alt="">
                        </div>
                        <div>
                            <div class="username">{{ auth()->user()->name }}</div>
                            <div class="title">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <luna-menu></luna-menu>
                </div>
            </nav>

            <div role="main" class="col-md-9 col-lg-10 px-4 mb-md-5" :class="{'ml-md-auto': user != null}">
                <router-view :key="$route.path"></router-view>
            </div>
        </div>
    </div>
</div>
</body>
</html>