<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="footer-py-60">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-12 mb-0 mb-md-4 pb-0 pb-md-2">
                            <a href="#" class="logo-footer">
                                <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="50" alt="">
                            </a>
                            <p class="mt-4">
                                {{ config('app.description') }}
                            </p>
                            <ul class="list-unstyled social-icon foot-social-icon mb-0 mt-4">
                                <li class="list-inline-item mb-0">
                                    <a href="{{ $infoApp->facebook }}" target="_blank" class="rounded">
                                        <i class="uil uil-facebook-f align-middle" title="facebook"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item mb-0">
                                    <a href="{{ $infoApp->instagram }}" target="_blank" class="rounded">
                                        <i class="uil uil-instagram align-middle" title="instagram"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item mb-0">
                                    <a href="mailto:{{ $infoApp->email }}" class="rounded">
                                        <i class="uil uil-envelope align-middle" title="email"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-lg-4 col-md-4 col-12 mt-4 mt-sm-0 pt-2 pt-sm-0">
                            <h5 class="footer-head">Tautan Eksternal</h5>
                            <ul class="list-unstyled footer-list mt-4">
                                <li>
                                    <a href="https://www.mahkamahagung.go.id/id" target="_blank" class="text-foot">
                                        <i class="uil uil-angle-right-b me-1"></i> Mahkamah Agung
                                    </a>
                                </li>
                                <li>
                                    <a href="https://badilum.mahkamahagung.go.id/" target="_blank" class="text-foot">
                                        <i class="uil uil-angle-right-b me-1"></i> Ditjen Badilum
                                    </a>
                                </li>
                                <li>
                                    <a href="https://pt-medan.go.id/" target="_blank" class="text-foot">
                                        <i class="uil uil-angle-right-b me-1"></i> Pengadilan Tinggi Medan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $infoApp->website }}" target="_blank" class="text-foot">
                                        <i class="uil uil-angle-right-b me-1"></i> {{ $infoApp->pengadilan_negeri }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-lg-4 col-md-4 col-12 mt-4 mt-sm-0 pt-2 pt-sm-0">
                            <h5 class="footer-head">Alamat</h5>
                            <p class="mt-4">
                                {{ $infoApp->alamat . ' ' . $infoApp->kabupaten . ' ' . $infoApp->provinsi . ' ' . $infoApp->kode_pos }}
                            </p>
                            <ul class="list-unstyled footer-list mt-0">
                                <li>
                                    <a href="tel:{{ $infoApp->kontak }}" class="text-foot">
                                        <i class="uil uil-phone me-1"></i> {{ $infoApp->kontak }}
                                    </a>

                                </li>
                                <li>
                                    <a href="mailto:{{ $infoApp->email }}" class="text-foot">
                                        <i class="uil uil-envelope me-1"></i> {{ $infoApp->email }}
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-py-30 footer-bar">
        <div class="container text-center">
            <div class="text-sm-start">
                <p class="mb-0">© 2021 -
                    <script>
                        document.write(new Date().getFullYear())
                    </script> {{ config('app.name') }}. Powered <i class="mdi mdi-heart text-danger"></i>
                    by
                    <a href="{{ $infoApp->website }}" target="_blank" class="text-reset">{{ $infoApp->pengadilan_negeri }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>
