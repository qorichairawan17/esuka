<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Judul diambil dari data yang dikirim Job --}}
    <title>{{ $title ?? '' }}</title>
    @include('miscellaneous.meta')
    <style type="text/css">
        * {
            font-family: 'Instrument Sans', sans-serif;
        }

        .table-barcode {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            border: 0.2px;
            border-style: solid;
            border-color: black;
            justify-content: center;
        }

        .img-barcode {
            max-width: 250px;
        }

        .qr-barcode {
            margin-top: 10px;
            max-width: 80px;
        }

        .danger-text {
            color: red;
        }

        .kuasa-text {
            margin: 0;
        }

        .fill-text {
            margin: 0;
        }

        .address-text {
            margin: 0;
            font-size: 12px;
        }

        .bukti-text {
            text-align: center;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <table class="table-barcode" border="1" cellpadding="5">
        <tr>
            <td width="150px;">
                {{-- Menggunakan public_path() lebih andal untuk gambar di PDF --}}
                <img class="img-barcode" src="{{ public_path('icons/horizontal-e-suka.png') }}" alt="Logo e-Suka">
            </td>
            <td style="text-align:center;">
                <h3 style="text-transform:uppercase; margin:0;">{{ $infoApp->pengadilan_negeri }}</h3>
                <p class="address-text">Jalan Jenderal Sudirman No 58 Lubuk Pakam
                    <br>
                    {{ $infoApp->website }} | {{ $infoApp->kontak }} | {{ $infoApp->email }}
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h2 class="bukti-text">Bukti Pendaftaran Surat Kuasa Elektronik</h2>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h4 class="kuasa-text">Tanggal Pendaftaran</h4>
                <p class="fill-text">{{ \Carbon\Carbon::parse($pendaftaran->tanggal_daftar)->isoFormat('dddd, D MMMM Y') }} ({{ $pendaftaran->created_at }})</p>
                <br>
                <h4 class="kuasa-text">Nomor Surat Kuasa</h4>
                <p class="fill-text">{{ $register->nomor_surat_kuasa }}</p>
                <br>
                <h4 class="kuasa-text">Pemberi Kuasa</h4>
                <p class="fill-text">{{ $pendaftaran->pihak->where('jenis', 'Pemberi')->pluck('nama')->join(', ') }}</p>
                <br>
                <h4 class="kuasa-text">Penerima Kuasa</h4>
                <p class="fill-text">{{ $pendaftaran->pihak->where('jenis', 'Penerima')->pluck('nama')->join(', ') }}</p>
            </td>
        </tr>
        <tr>
            <td style="text-align:center;">
                <h4 class="fill-text">Panitera <br> {{ $infoApp->pengadilan_negeri }}</h4>
                {{-- Embed QR Code dari base64 string --}}
                <img class="qr-barcode" src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                <p class="fill-text">
                    <span style="text-decoration: underline;">{{ $register->panitera->nama }}</span>
                    <br>
                    NIP. {{ $register->panitera->nip }}
                </p>
            </td>
            <td>
                <p>Scan QRCode disamping untuk melihat bukti Pendaftaran Surat Kuasa melalui {{ config('app.name') }}
                    <br>
                    Atau melalui link : <a href="{{ $qrCodeUrl }}">{{ $qrCodeUrl }}</a>
                </p>
                <p class="danger-text">
                    Cetak lembar ini dan satukan dengan surat kuasa yang didaftarkan !
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p style="font-size: 10px; margin:0;">
                    Dicetak : {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y HH:mm:ss') }} | <i>Powered by : {{ $infoApp->pengadilan_negeri }}</i>
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
