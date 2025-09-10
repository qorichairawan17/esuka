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
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                            <h6 class="card-title mb-0">Data Testimoni</h6>
                        </div>
                        <div class="card-body">
                            <form class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search"
                                            placeholder="Cari Surat Kuasa...">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Testimoni</th>
                                            <th>Rating</th>
                                            <th>Publish</th>
                                            <th>Aksi</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align: top;">1</td>
                                            <td style="vertical-align: top;">Ahmad Supriadi</td>
                                            <td style="vertical-align: top;">
                                                ahmad@gmail.com
                                            </td>
                                            <td style="vertical-align: top;">
                                                Aplikasinya mantap sangat sesuai dengan kebutuhan
                                            </td>
                                            <td style="vertical-align: top;">
                                                @for ($i = 0; $i < 5; $i++)
                                                    <i class="uil uil-star text-warning"></i>
                                                @endfor
                                            </td>
                                            <td style="vertical-align: top;">
                                                <span class="badge bg-success">Aktif</span>
                                            </td>
                                            <td style="vertical-align: top;">
                                                <a href="{{ route('surat-kuasa.detail') }}"
                                                    class="btn btn-sm btn-pills btn-soft-warning mb-2">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <a href="" class="btn btn-sm btn-pills btn-danger mb-2">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <ul class="pagination mb-0">
                                <li class="page-item"><a class="page-link" href="javascript:void(0)"
                                        aria-label="Previous">Prev</a></li>
                                <li class="page-item active"><a class="page-link" href="javascript:void(0)">1</a></li>
                                <li class="page-item"><a class="page-link" href="javascript:void(0)">2</a></li>
                                <li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
                                <li class="page-item"><a class="page-link" href="javascript:void(0)"
                                        aria-label="Next">Next</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
        <!-- End -->
    </main>
    <!--End page-content" -->
@endsection
