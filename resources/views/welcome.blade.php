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
                <span class="fs-4">{{ env('APP_NAME') }} verifikasi nomor</span>
            </a>
        </header>

        <main>
            {{-- Tutorial --}}
            <div class="row g-5 mb-4">
                <div class="col-md-6">
                    <h2 class="text-body-emphasis">How to use</h2>
                    <p>Tolong baca tutorial singkat ini agar proses dilakukan dengan lancar.</p>
                    <ul class="list-unstyled ps-0">
                        <li class="mb-2">
                            <div class="d-flex align-items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-info-circle me-2" viewBox="0 0 16 16">
                                    <path
                                        d="M8 7.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2a.5.5 0 0 1 .5-.5zM8 4a4 4 0 1 0 0 8A4 4 0 0 0 8 4z" />
                                </svg>
                                <div>
                                    <strong>Step 1:</strong> Input nomor Anda pada kolom input nomor, lalu minta kode
                                    OTP dengan menekan tombol "Minta OTP".
                                </div>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex align-items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-info-circle me-2" viewBox="0 0 16 16">
                                    <path
                                        d="M8 7.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2a.5.5 0 0 1 .5-.5zM8 4a4 4 0 1 0 0 8A4 4 0 0 0 8 4z" />
                                </svg>
                                <div>
                                    <strong>Step 2:</strong> Anda akan mendapatkan beberapa informasi nomor dan nama
                                    file Anda pada kolom "Debug Console" yang ada di bagian bawah halaman.
                                </div>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex align-items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-info-circle me-2" viewBox="0 0 16 16">
                                    <path
                                        d="M8 7.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2a.5.5 0 0 1 .5-.5zM8 4a4 4 0 1 0 0 8A4 4 0 0 0 8 4z" />
                                </svg>
                                <div>
                                    <strong>Step 3:</strong> Terakhir, masukkan kode OTP yang didapatkan di SMS pada
                                    kolom input yang sesuai.
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>


            <hr class="my-4">

            {{-- Form Validasi --}}
            <div class="row g-5">

                {{-- Form Input Number --}}
                <div class="col-md-5 col-lg-4 order-md-last mb-4">
                    <form class="needs-validation" method="POST" action="{{ route('send-number') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="nomor" class="form-label">XL phone number</label>
                            <div class="input-group has-validation">
                                <input type="text" class="form-control" name="nomor"
                                    value="{{ $body['number'] ?? null }}" id="nomor" placeholder="0819xxxx" required>
                                <div class="invalid-feedback">
                                    Your number is required.
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <button class="btn btn-primary btn-lg" type="submit">Minta OTP</button>
                    </form>
                </div>
                
                {{-- Form Input OTP --}}
                <div class="col-md-7 col-lg-8 mb-4">
                    <h2>Verifikasi</h2>
                    <form class="needs-validation" method="POST" action="{{ route('send-otp') }}">
                        @csrf
                        <input type="hidden" class="form-control" name="nomor" value="{{ $body['number'] ?? null }}">

                        <div class="mb-3">
                            <label for="otp" class="form-label">Input OTP</label>
                            <div class="input-group has-validation">
                                <input type="text" class="form-control" name="otp" id="otp" required>
                                <div class="invalid-feedback">
                                    Your OTP is required.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            {{-- <label for="file" class="form-label">File name</label> --}}
                            <div class="input-group has-validation">
                                <input type="hidden" class="form-control" value="{{ $body['filename'] ?? null }}" name="file" id="file" required>
                                <div class="invalid-feedback">
                                    Your file name is required.
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <button class="btn btn-primary btn-lg" type="submit">Submit OTP</button>
                    </form>
                </div>
            </div>

            <hr class="my-4">

            {{-- Console --}}
            {{-- <div class="mb-4">
                <div class="form-floating">
                    <textarea class="form-control" placeholder="Show output here" id="floatingTextarea2" style="height: 200px" readonly>{{ $massage ?? null }}</textarea>
                    <label for="floatingTextarea2">Debug Console</label>
                </div>
            </div> --}}
        </main>

    </div>

    {{-- Bootstrap 5 JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>
