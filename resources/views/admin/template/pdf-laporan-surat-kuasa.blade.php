<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Laporan Surat Kuasa' }}</title>
    <style type="text/css">
        * {
            font-family: 'Instrument Sans', sans-serif;
        }

        body {
            margin: 20px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-section h2 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header-section h3 {
            margin: 3px 0;
            font-size: 14px;
        }

        .header-section p {
            margin: 2px 0;
            font-size: 11px;
        }

        .info-section {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .info-section table {
            width: 100%;
        }

        .info-section td {
            padding: 2px 0;
        }

        .table-laporan {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        .table-laporan thead {
            background-color: #f3f4f6;
        }

        .table-laporan th {
            border: 1px solid #000;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
        }

        .table-laporan td {
            border: 1px solid #000;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

         .text-left {
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .footer-section {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
        }

        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header-section">
        <h2 style="text-transform: uppercase;">{{ $infoApp->pengadilan_negeri ?? 'Pengadilan Negeri' }}</h2>
        <h3>LAPORAN PENDAFTARAN SURAT KUASA</h3>
        <p>{{ $infoApp->alamat ?? '' }}</p>
    </div>

    <hr>

    {{-- Info Filter --}}
    <div class="info-section">
        <table>
            <tr>
                <td style="width: 120px;">Tanggal Cetak</td>
                <td style="width: 10px;">:</td>
                <td>{{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td>Filter Status</td>
                <td>:</td>
                <td>{{ $filterStatus ? ucfirst($filterStatus) : 'Semua Status' }}</td>
            </tr>
            <tr>
                <td>Filter Tahun</td>
                <td>:</td>
                <td>{{ $filterTahun ?? 'Semua Tahun' }}</td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td>:</td>
                <td><strong>{{ $laporanData->count() }} pendaftaran</strong></td>
            </tr>
        </table>
    </div>

    {{-- Table Laporan --}}
    <table class="table-laporan">
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th>Tanggal Daftar</th>
                <th style="width: 80px;">ID Pendaftaran</th>
                <th>Pemohon</th>
                <th>Nomor Surat Kuasa</th>
                <th>Pemberi Kuasa</th>
                <th>Penerima Kuasa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporanData as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_daftar)->format('d/m/Y') }}</td>
                    <td>{{ $item->id_daftar }}</td>
                    <td>{{ $item->pemohon ?? 'N/A' }}</td>
                    <td class="text-left">{{ $item->register->nomor_surat_kuasa ?? '-' }}</td>
                    <td>{{ $item->pihak->where('jenis', \App\Enum\PihakSuratKuasaEnum::Pemberi->value)->pluck('nama')->join(', ') }}</td>
                    <td>{{ $item->pihak->where('jenis', \App\Enum\PihakSuratKuasaEnum::Penerima->value)->pluck('nama')->join(', ') }}</td>
                    <td class="text-center">
                        @if ($item->status === \App\Enum\StatusSuratKuasaEnum::Disetujui->value)
                            <span class="badge badge-success">{{ $item->status }}</span>
                        @elseif($item->status === \App\Enum\StatusSuratKuasaEnum::Ditolak->value)
                            <span class="badge badge-danger">{{ $item->status }}</span>
                        @else
                            {{ $item->status }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer-section">
        <p><i>Dicetak dari {{ config('app.name') }} - {{ $infoApp->pengadilan_negeri ?? '' }}</i></p>
    </div>
</body>

</html>
