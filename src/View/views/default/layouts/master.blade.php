<!DOCTYPE html>
<html>
<head>
    @yield('head')
</head>
<body>
    <header>
        <div class="container">
            @yield('header.top')
            @yield('header')
            @yield('header.bottom')
        </div>
    </header>
    <main>
        <div class="container">
            @yield('main.top')
            @include('layouts.elements.messages')
            @yield('main')
            @yield('main.bottom')
        </div>
    </main>
    <footer>
        <div class="container">
            @yield('footer.top')
            @yield('footer')
            @yield('footer.bottom')
        </div>
    </footer>
</body>
</html>