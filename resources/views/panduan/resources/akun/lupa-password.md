#### Lupa Password

Pastikan Email yang kamu miliki merupakan Email yang terdaftar pada {{ config('app.name') }}.

---

1. **Buka halaman lupa password.** dengan mengklik tombol login kemudian memilih, Lupa Password atau klik berikut ini <a href="{{route('auth.forgot-password')}}">{{route('auth.forgot-password')}}</a>.

    <img class="img-fluid mb-2" src="{{ asset('assets/images/panduan/akun/forgot-password.png') }}">

2. **Masukan email Kamu dan tekan tombol kirim**, Kamu akan mendapat email masuk yang berisi link tautan untuk mereset password.

    <img class="img-fluid mb-2" src="{{ asset('assets/images/panduan/akun/sending-email-forgot-password.png') }}">

3. **Klik tombol Reset Password** untuk mereset password, link hanya berlaku 60 menit.
    
    <img class="img-fluid mb-2" src="{{ asset('assets/images/panduan/akun/email-reset-password.png') }}">

4. **Silahkan isi password baru Kamu**. Password harus terdiri dari gabungan Huruf kapital,angka dan karakter. Kemudian klik tombol Simpan

    <img class="img-fluid mb-2" src="{{ asset('assets/images/panduan/akun/new-password.png') }}">

4. **Setelah berhasil mereset password**. Silahkan login dengan menggunakan password baru.
    <img class="img-fluid mb-2" src="{{ asset('assets/images/panduan/akun/success-reset-password.png') }}">

---

