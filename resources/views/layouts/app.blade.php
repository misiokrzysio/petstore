<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="g-2 mt-1">
            <a href="{{ route('pet.list') }}" class="btn btn-primary">
                Lista
            </a>
            <a href="{{ route('pet.create') }}" class="btn btn-primary">
                Tworzenie
            </a>
        </div>
        <hr />
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
