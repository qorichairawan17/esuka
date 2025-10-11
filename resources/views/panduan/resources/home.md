#### Panduan Penggunaan {{ config('app.name') }}

Kami menyediakan panduan ini untuk membantu Kamu memahami cara menggunakan aplikasi, mulai dari proses pendaftaran akun, pengelolaan data pengguna, pengajuan surat kuasa, hingga langkah-langkah memanfaatkan seluruh fitur aplikasi dengan mudah.

<table class="table">
    <tbody>
        <tr>
            <th>Aplikasi</th>
            <td>{{ config('app.name') }}</td>
        </tr>
        <tr>
            <th>Versi</th>
            <td>{{ config('app.version') }}</td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td>{{ config('app.description') }}</td>
        </tr>
        <tr>
            <th>Developer</th>
            <td>{{ config('app.developer') }}</td>
        </tr>
        <tr>
            <th>Rilis Panduan</th>
            <td>{{ \Carbon\Carbon::parse('2025-01-09')->isoFormat('dddd, D MMMM Y') }}</td>
        </tr>
    </tbody>
</table>

<a class="btn btn-soft-primary btn-sm" href="{{ route('app.signin') }}">Akses Sekarang</a>
