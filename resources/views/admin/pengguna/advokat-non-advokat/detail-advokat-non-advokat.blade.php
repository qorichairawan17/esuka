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
                            <h6 class="card-title mb-0 text-dark">{{ $detailTitle }}</h6>
                            <a href="{{ route('advokat.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    @if (isset($user) && $user->profile->foto)
                                        <img src="{{ asset('storage/' . $user->profile->foto) }}" class="img-thumbnail mt-2" style="max-width: 200px;" alt="">
                                    @else
                                        <img src="{{ asset('assets/images/user/user-none.png') }}" class="img-thumbnail mt-2" style="max-width: 200px;" alt="">
                                    @endif
                                </div>
                                <div class="col-lg-9 col-md-6 col-sm-12">
                                    <ul class="list-unstyled">
                                        <li class="border-bottom py-2">Nama: {{ $user->name }}</li>
                                        <li class="border-bottom py-2">Email: {{ $user->email }}</li>
                                        <li class="border-bottom py-2">Tanggal Lahir: {{ $user->profile->tanggal_lahir ? \Carbon\Carbon::parse($user->profile->tanggal_lahir)->format('d-m-Y') : '' }}</li>
                                        <li class="border-bottom py-2">Jenis Kelamin: {{ $user->profile->jenis_kelamin }}</li>
                                        <li class="border-bottom py-2">Kontak: {{ $user->profile->kontak }}</li>
                                        <li class="border-bottom py-2">Alamat: {{ $user->profile->alamat }}</li>
                                        <li class="border-bottom py-2">Status Akun: <span class="badge bg-{{ $user->block ? 'danger' : 'success' }}">{{ $user->block ? 'Diblokir' : 'Aktif' }}</span></li>
                                        <li class="border-bottom py-2">Setuju Privacy Policy: {{ $user->privacy_policy_agreed_at }}</li>
                                        <li class="border-bottom py-2">Created At: {{ $user->created_at }} | Updated At: {{ $user->updated_at }}</li>
                                    </ul>
                                </div>
                            </div>
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
