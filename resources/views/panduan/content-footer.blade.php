<footer class="shadow py-4 mt-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="text-md-start text-center mx-md-2">
                    <p class="mb-0 text-muted d-flex align-items-center justify-content-center justify-content-md-start gap-1">
                        <span>© 2021 -</span>
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                        <span class="fw-semibold text-dark">{{ config('app.name') }}</span>
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-md-end text-center mx-md-2 mt-2 mt-md-0">
                    <p class="mb-0 text-muted">
                        Developed By
                        <a href="{{ $infoApp->website }}" target="_blank" class="text-primary text-decoration-none fw-medium">
                            {{ $infoApp->pengadilan_negeri }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
