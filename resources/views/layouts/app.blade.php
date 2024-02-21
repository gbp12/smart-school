<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <title>Smart School!</title>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>

    <header>
        este es el header de la app,
    </header>
    <div id="viewTitle">pene</div>

    <main class="container-fluid">
        @yield('content')
    </main>
    <footer> este es el footer de la app</footer>

</body>

</html>
