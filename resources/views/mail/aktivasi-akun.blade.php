@include('mail.layouts.header')

<body>
    <div style="margin-top: 50px;">
        <table cellpadding="0" cellspacing="0"
            style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
            <thead>
                <tr style="background-color: #2f55d4; padding: 3px 0; border: none; line-height: 68px; text-align: center; color: #fff; font-size: 16px; letter-spacing: 1px;">
                    <th scope="col">{{ $title ?? 'Aktivasi Akun' }} </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="padding: 48px 24px 0; color: #161c2d; font-size: 18px; font-weight: 600;">
                        Hallo, {{ $user->name ?? 'Pengguna' }}
                    </td>
                </tr>
                @if (isset($user) && $user->reactivation == 1)
                    <tr>
                        <td style="padding: 15px 24px 15px;text-align: justify;">
                            Kamu sebelumnya telah terdaftar pada layanan {{ config('app.name') }} versi lama, untuk itu silahkan untuk reaktivasi akun Kamu dan gunakan Password baru kamu :
                            <b>{{ $newPassword ?? '**********' }}</b> untuk masuk kedalam aplikasi. Harap jangan membagikan kepada siapapun Password ini !.
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding: 15px 24px 15px;text-align: justify;">
                        Terimakasih telah mendaftar di layanan <b>{{ config('app.name') }}</b>. {{ config('app.author') }}. Silahkan klik tombol di bawah ini untuk
                        aktivasi akun Kamu.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px 24px;">
                        <a href="{{ $activationUrl ?? '#' }}"
                            style="padding: 8px 20px; outline: none; text-decoration: none; font-size: 16px; letter-spacing: 0.5px; transition: all 0.3s; font-weight: 600; border-radius: 6px; background-color: #2f55d4; border: 1px solid #2f55d4; color: #ffffff;">
                            Aktivasi Akun
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px 24px 0;text-align: justify;">
                        Link ini hanya berlaku selama <b>60 menit</b>. Jika Kamu tidak melakukan aktivasi akun, silahkan abaikan email ini.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px 24px 15px;">
                        {{ config('app.name') }} <br> Developed by {{ config('app.author') }} <br>
                        <span style="color: red; font-size: 12px;">Email ini dikirim otomatis oleh sistem, mohon untuk tidak membalas email ini.</span>
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
