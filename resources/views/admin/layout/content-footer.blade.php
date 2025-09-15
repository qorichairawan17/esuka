<footer class="shadow py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <div class="text-sm-start text-center mx-md-2">
                    <p class="mb-0 text-muted">2021 -
                        <script>
                            document.write(new Date().getFullYear())
                        </script> &copy; {{ config('app.name') }}. Powered by
                        <a href="{{ $infoApp->website }}" target="_blank" class="text-primary">{{ $infoApp->pengadilan_negeri }}</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
