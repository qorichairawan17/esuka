<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sendLinkForm = document.getElementById('send-link-form');
        if (sendLinkForm) {
            sendLinkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(this, '{{ route('auth.forgot-password.send') }}');
            });
        }

        const resetPasswordForm = document.getElementById('reset-password-form');
        if (resetPasswordForm) {
            resetPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(this, '{{ route('auth.forgot-password.save') }}');
            });
        }

        function handleFormSubmit(form, url) {
            const formData = new FormData(form);
            const button = form.querySelector('button[type="submit"]');
            const originalButtonText = button.innerHTML;

            // Clear previous errors
            document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    button.disabled = true;
                    button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;
                }
            });

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    button.disabled = false;
                    button.innerHTML = originalButtonText;

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                        }).then(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        });
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const errorElement = document.getElementById(key + 'Error');
                                if (errorElement) {
                                    errorElement.textContent = data.errors[key][0];
                                }
                            });
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message || 'Terjadi kesalahan.',
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    button.disabled = false;
                    button.innerHTML = originalButtonText;
                    Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
                });
        }
    });
</script>
