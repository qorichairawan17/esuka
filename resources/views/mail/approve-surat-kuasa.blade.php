@include('mail.layouts.header')

<body>
    <div style="margin-top: 50px;">
        <table cellpadding="0" cellspacing="0"
            style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
            <thead>
                <tr style="background-color: #2f55d4; padding: 3px 0; border: none; line-height: 68px; text-align: center; color: #fff; font-size: 16px; letter-spacing: 1px;">
                    <th scope="col">Pendaftaran Surat Kuasa Disetujui</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="padding: 48px 24px 0; color: #161c2d; font-size: 18px; font-weight: 600;">
                        Hallo, {{ $user->name ?? 'Pengguna' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px 24px 15px;text-align: justify;">
                        Selamat! Pendaftaran surat kuasa Anda dengan ID Pendaftaran:
                        <b>{{ $suratKuasa->id_daftar }}</b> telah disetujui dan diregistrasi dengan nomor:
                        <b>{{ $suratKuasa->register->nomor_surat_kuasa ?? '' }}</b>.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px 24px 15px;text-align: justify;">
                        Anda dapat mengunduh barcode pendaftaran elektronik yang terlampir pada email ini. Silakan
                        gunakan barcode tersebut untuk keperluan lebih lanjut.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px 24px 15px;">
                        {{ config('app.name') }} <br> Powered by {{ config('app.author') }} <br>
                        <span style="color: red; font-size: 12px;">Email ini dikirim otomatis oleh sistem, mohon untuk
                            tidak membalas email ini.</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 16px 8px;  background-color: #f8f9fc; text-align: center;">
                        © 2021 - {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
