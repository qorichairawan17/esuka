<script>
    document.getElementById('register-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Clear previous errors
        const clearErrors = () => {
            document.getElementById('namaDepanError').textContent = '';
            document.getElementById('namaBelakangError').textContent = '';
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';
        };

        clearErrors();

        // Disable button to prevent multiple submissions
        const button = document.getElementById('register-button');
        button.disabled = true;
        button.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mendaftar...';

        try {
            let response = await fetch("{{ route('auth.register') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            let result = await response.json();

            if (!response.ok) {
                if (response.status === 422 && result.errors) {
                    // Handle validation errors
                    const fields = ["namaDepan", "namaBelakang", "email", "password"];
                    fields.forEach(field => {
                        const errorElement = document.getElementById(field + "Error");
                        if (result.errors[field] && errorElement) {
                            errorElement.innerText = "*" + result.errors[field][0] ?? '';
                        }
                    });
                } else {
                    // Handle other server errors (e.g., 500)
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.message || 'Terjadi kesalahan pada server.',
                    });
                }
                return;
            }

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
                window.location.href = result.redirect;
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: result.message || 'Operasi tidak berhasil.',
                });
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tidak dapat menghubungi server. Periksa koneksi internet Anda.',
            });
        } finally {
            // Re-enable button
            button.disabled = false;
            button.innerHTML = 'Daftar';
        }
    });
</script>
