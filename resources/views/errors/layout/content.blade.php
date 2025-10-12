<div class="error-page-wrapper">
    <div class="error-card">
        <div class="error-decorations">
            <div class="decoration-circle decoration-circle-1"></div>
            <div class="decoration-circle decoration-circle-2"></div>
        </div>

        <div class="text-center">
            <img src="{{ asset('icons/horizontal-e-suka.png') }}" class="error-logo" alt="E-Suka Logo">

            <div class="error-icon">
                <i class="uil uil-exclamation-triangle"></i>
            </div>

            <h1 class="error-code">{{ $code }}</h1>

            <p class="error-message">
                {{ $message }}
            </p>

            <div class="error-actions">
                <a href="{{ url()->previous() == url()->current() ? route('app.home') : url()->previous() }}" class="btn-error btn-primary-gradient">
                    <i class="uil uil-arrow-left"></i>
                    Kembali
                </a>
                <a href="{{ route('app.home') }}" class="btn-error btn-outline-custom">
                    <i class="uil uil-home"></i>
                    Beranda
                </a>
            </div>
        </div>
    </div>
</div>
