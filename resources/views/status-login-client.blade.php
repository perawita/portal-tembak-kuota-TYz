<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('APP_NAME') }}</title>

    {{-- icon --}}
    <link href="{{ asset('/vendor/orchid/favicon.svg') }}" sizes="any" type="image/svg+xml" id="favicon" rel="icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex flex-column h-100">

    {{-- index tampilan --}}
    <div class="container py-4 py-md-5">

        <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
            <a href="/" class="d-flex align-items-center text-body-emphasis text-decoration-none">
                <span class="fs-4">{{ env('APP_NAME') }}</span>
            </a>
        </header>

        <main>
            <div class="px-4 py-5 my-5 text-center">
              {{-- <img class="d-block mx-auto mb-4" src="{{ asset('/vendor/orchid/favicon.svg') }}" alt="" width="72" height="57"> --}}
              <h1 class="display-5 fw-bold">Informasi</h1>
              <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">{{$massage}}</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                  <a href="/" class="btn btn-outline-secondary btn-lg px-4">Back to home</a>
                </div>
              </div>
            </div>
          
            <div class="b-example-divider"></div>
          
          </main>

    </div>

    {{-- Bootstrap 5 JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>
