<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
            <td width="100px;">
                <img class="img-barcode" src="{{ asset('icons/horizontal-e-suka.png') }}" alt="">
            </td>
            <td style="text-align:center;">
                <h3 style="text-transform:uppercase; margin:0;">{{ config('app.author') }}</h3>
                <p class="address-text">Jalan Jenderal Sudirman No 58 Lubuk Pakam
                    <br>
                    www.pn-lubukpakam.go.id | 0812-3456-789 | pnlubukpakam@yahoo.co.id
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h2 class="bukti-text">Bukti Pendaftaran Surat Kuasa</h2>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h4 class="kuasa-text">Tanggal Pendaftaran</h4>
                <p class="fill-text">{{ \Carbon\Carbon::now()->format('d-m-Y') }} - {{ \Carbon\Carbon::now() }}</p>
                <br>
                <h4 class="kuasa-text">Nomor Surat Kuasa</h4>
                <p class="fill-text">192/SK/VII/2021/PN.Lbp</p>
                <br>
                <h4 class="kuasa-text">Pemberi Kuasa</h4>
                <p class="fill-text">Yani Ahmad Zakir</p>
                <br>
                <h4 class="kuasa-text">Penerima Kuasa</h4>
                <p class="fill-text">Ahmad Dofiri</p>
            </td>
        </tr>
        <tr>
            <td style="text-align:center;">
                <h4 class="fill-text">Panitera <br> {{ config('app.author') }}</h4>
                <img class="qr-barcode" src="{{ asset('images/barcode.png') }}" alt="">
                <p class="fill-text">Syawal Aswad Siregar <br>
                    NIP. 121974687676
                </p>
            </td>
            <td>
                <p>Scan QRCode disamping untuk melihat bukti Pendaftaran Surat Kuasa melalui {{ config('app.name') }}
                    <br>
                    Atau melalui link :<a> {{ url()->current() }}</a>
                </p>
                <p class="danger-text">
                    Cetak lembar ini dan satukan dengan surat kuasa yang didaftarkan !
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p>
                    Dicetak : {{ \Carbon\Carbon::now() }} | <i>Powered by : {{ config('app.author') }}</i>
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
