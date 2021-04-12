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
            window.luna = JSON.parse('@json(Luna::export())');
        @endif
    </script>
    <script src="/luna/js/index.js" defer></script>
</head>
<body>
<div id="app" class="rtl" style="display: none">
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

                    <div class="p-2 mx-2 mb-1 sidebar-heading border rounded">
                        <h6 class="p-0 m-0 d-flex justify-content-start align-items-center">
                            <i class="fa fa-database mr-2"></i>
                            <span>منابع</span>
                        </h6>
                    </div>

                    <ul class="nav flex-column mb-1">
                        <li class="nav-item" v-for="resource in resources" v-if="resource.visible">
                            <router-link class="nav-link"
                                         :to="{name: 'resources', params: {resource: resource.name}}">
                                <span v-html="resource.plural"></span>
                            </router-link>
                        </li>
                    </ul>

                    <div v-if="Object.keys(tools).length > 0">
                        <div class="p-2 mx-2 mb-1 sidebar-heading border rounded">
                            <h6 class="p-0 m-0 d-flex justify-content-start align-items-center">
                                <i class="fa fa-puzzle-piece mr-2"></i>
                                <span>افزونه ها</span>
                            </h6>
                        </div>

                        <ul class="nav flex-column">
                            <li class="nav-item" v-for="tool in tools">
                                <router-link class="nav-link"
                                             :to="{name: 'tools', params: {name: tool.name}}">
                                    <span v-html="tool.title"></span>
                                </router-link>
                            </li>
                        </ul>
                    </div>
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
