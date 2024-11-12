<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laravel')</title>  
    

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js','resources/js/get_dates_title.js','resources/js/toggle_list_and_chart.js'])
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/paginationjs@2.1.5/dist/pagination.min.js"></script>

</head>
<body class="font-sans antialiased">

    @include('partials.navbar')  <!-- Barra de navegación común -->

    <div class="container mx-auto">
        @yield('content')  <!-- Aquí se inyectará el contenido de las vistas hijas -->
    </div>

</body>

</html>
