@extends('admin.layout.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('admin.component.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <div class="mt-4">
                    <div class="card shadow">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between bg-soft-primary">
                            <h6 class="card-title mb-0 text-dark">Data Testimoni</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table table-bordered table-hover', 'style' => 'width:100%;font-size:14px;']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
        <!-- End -->

        <!-- Modal Edit Testimoni -->
        <div class="modal fade" id="editTestimoniModal" tabindex="-1" aria-labelledby="editTestimoniModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTestimoniModalLabel">Edit Testimoni</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-testimoni-form">
                            @csrf
                            <input type="hidden" id="testimoni_id" name="id">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value="1">1 Bintang</option>
                                    <option value="2">2 Bintang</option>
                                    <option value="3">3 Bintang</option>
                                    <option value="4">4 Bintang</option>
                                    <option value="5">5 Bintang</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="pesan" class="form-label">Pesan Testimoni</label>
                                <textarea class="form-control" id="pesan" name="pesan" rows="4" required maxlength="500"></textarea>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="publish" name="publish" value="1">
                                <label class="form-check-label" for="publish">Publikasikan Testimoni</label>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" id="btn-update-testimoni" class="btn btn-sm btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!--End page-content" -->
@endsection
@push('scripts')
    {!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
    <script type="module">
        const editModal = new bootstrap.Modal(document.getElementById('editTestimoniModal'));

        window.showEditModal = async function(id) {
            const url = `{{ route('testimoni.edit', ['id' => ':id']) }}`.replace(':id', id);

            try {
                const response = await fetch(url);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal mengambil data.');
                }

                document.getElementById('testimoni_id').value = id;
                document.getElementById('rating').value = data.rating;
                document.getElementById('pesan').value = data.testimoni;
                document.getElementById('publish').checked = data.publish_at !== null;
                editModal.show();
            } catch (error) {
                Swal.fire('Error!', error.message, 'error');
            }
        }

        document.getElementById('edit-testimoni-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = this;
            const button = document.getElementById('btn-update-testimoni');
            const originalButtonHtml = button.innerHTML;

            button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
            button.disabled = true;

            const formData = new FormData(form);
            const url = `{{ route('testimoni.update', ['id' => ':id']) }}`.replace(':id', formData.get('id'));

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData.get('_token')
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Gagal memperbarui data.');
                }

                editModal.hide();
                await Swal.fire('Berhasil!', result.message, 'success');
                window.LaravelDataTables['testimoni-table'].ajax.reload();
            } catch (error) {
                Swal.fire('Error!', error.message, 'error');
            } finally {
                button.innerHTML = originalButtonHtml;
                button.disabled = false;
            }
        });
    </script>
@endpush
