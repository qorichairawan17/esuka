<script>
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Clear previous errors
        const clearErrors = () => {
            document.getElementById('emailError').innerText = '';
            document.getElementById('passwordError').innerText = '';
            document.getElementById('captchaError').innerText = '';
        };

        clearErrors();

        // Disable button to prevent multiple submissions
        const submitButton = document.getElementById('login-button');
        submitButton.disabled = true;
        submitButton.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

        try {
            let response = await fetch("{{ route('auth.login') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            let result = await response.json();

            if (result.success) {
                Swal.fire({
                    title: 'Login berhasil!',
                    text: result.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    timerProgressBar: true,
                }).then(() => {
                    window.location.href = result.redirect;
                });
            } else {
                // Refresh captcha on any error
                document.getElementById('captcha-img').click();
                document.getElementById('password').value = '';
                document.getElementById('captcha').value = '';

                if (result.errors) {
                    // Handle validation errors
                    const fields = ["email", "password", "captcha"];
                    fields.forEach(field => {
                        const errorElement = document.getElementById(field + "Error");
                        if (result.errors[field] && errorElement) {
                            errorElement.innerText = "*" + result.errors[field][0] ?? '';
                        }
                    });
                } else {
                    // Handle other errors (e.g., wrong credentials, blocked account)
                    Swal.fire({
                        title: 'Login Gagal!',
                        text: result.message,
                        icon: 'error'
                    });
                }
            }
        } catch (error) {
            console.error('Login request failed:', error);
            Swal.fire({
                title: 'Oops...',
                text: 'Terjadi kesalahan. Silakan coba lagi.',
                icon: 'error'
            });
        } finally {
            // Re-enable button
            submitButton.disabled = false;
            submitButton.innerHTML = 'Masuk';
        }
    });

    document.getElementById('captcha-img').onclick = function() {
        document.getElementById('captcha-img').src = "{{ captcha_src('flat') }}" + "?" + Date.now();
    };
</script>
