<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.terminal/2.41.1/css/jquery.terminal.min.css"
        rel="stylesheet" />

</head>

<body>
    @include('layouts.card')

    <script>
        window.addEventListener('error', function(event) {
            if (event.message.includes("$ is not defined")) {
                alert('Error: Terjadi error pada query tolong refres halaman ini setelah halaman ini di muat.');
            }
        });

        // document.getElementById('button').style.display = 'none';
        document.getElementById('refres').addEventListener('click', function() {
            location.reload();
        });

        $(document).ready(function() {
            let paymentod = '0';
            switch ('{{ $payment }}') {
                case 'Dana':
                    paymentod = '1';
                    break;

                case 'Gopay':
                    paymentod = '2';
                    break;

                case 'Balance':
                    paymentod = '3';
                    break;

                default:
                    alert('Error: Invalid payment method');
                    break;
            }

            // Meminta request untuk mengeksekusi script yang dipilih
            $.ajax({
                url: "{{ route('platform.rund.script') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: {
                    path: "{{ $path }}",
                    payment: paymentod,
                },
                dataType: 'json',
                success: function(response) {
                    try {
                        // Mengurai lapisan pertama
                        if (typeof response['xdg-open'] === 'string') {
                            response['xdg-open'] = JSON.parse(response['xdg-open']);
                        }

                        // Mengurai lapisan kedua (decryptedData)
                        if (typeof response['xdg-open'].decryptedData === 'string') {
                            response['xdg-open'].decryptedData = JSON.parse(response['xdg-open']
                                .decryptedData);
                        }

                        const decryptedData = response['xdg-open'].decryptedData;

                        console.log(decryptedData);
                        
                        $('#code').html(decryptedData['code'] + ' ' + decryptedData['status']);
                        $('#massage').html(decryptedData['message'] ?? decryptedData['data']['deeplink']);

                        const url_payments = decryptedData['data']['deeplink'];

                        if (decryptedData['status'] === 'SUCCESS') {
                            fetch("{{ route('platform.update.wallet') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        id: {{ $id }}
                                    })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(response => {
                                    fetch("{{ route('platform.create.history') }}", {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                id_quota: {{ $id }},
                                                url_payment: url_payments
                                            })
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Network response was not ok');
                                            }
                                            return response.json();
                                        })
                                        .then(response => {
                                            alert(
                                                'Information: Pembelian berhasil dilakukan saldo anda sudah terpotong'
                                            );
                                            fetch("{{ route('platform.update.history') }}", {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Content-Type': 'application/json'
                                                    },
                                                    body: JSON.stringify({
                                                        id_history: response
                                                            .history_new_key
                                                    })
                                                })
                                                .then(success => {
                                                    $('#button').hide();
                                                    window.open(url_payments, '_blank');
                                                })
                                                .catch(error => {
                                                    console.error(
                                                        'There was a problem update history:',
                                                        error);
                                                });
                                        })
                                        .catch(error => {
                                            console.error(
                                                'There was a problem create history:',
                                                error);
                                        });
                                })
                                .catch(error => {
                                    console.error(
                                        'There was a problem updating wallet:',
                                        error);
                                });
                        } else {
                            document.getElementById('button').style.display = 'block';
                            document.getElementById('button').setAttribute('target', '_blank');
                            document.getElementById('button').setAttribute('href',
                                'https://wa.me/6287738915986'); //ganti dengan nomor Anda

                        }
                    } catch (error) {
                        console.error('Error processing response:', error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Transaction failed:', error);
                    $('#response').html('Transaction failed: ' + error);
                }
            });

            // Melakukan request untuk menghapus sesi pembeli apabila request di atas sukses dilakukan
            $.ajax({
                url: "{{ route('platform.clear.session') }}",
                type: 'GET',
                async: false,
                error: function(xhr, status, error) {
                    console.error('Clearing session failed:', error);
                }
            });

        });
    </script>


    @php
        $id = request()->query('encryptedId');
    @endphp

</body>

</html>
