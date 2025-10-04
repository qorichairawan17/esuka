<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;
use App\Http\Requests\Profile\UpdatePhotoRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;

class ProfileController extends Controller
{
    protected $infoApp, $profileService;

    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });

        $this->profileService = new ProfileService();
    }

    private function breadCumb($parameters)
    {
        $dashboardRoute = Auth::user()->role === 'User' ? route('dashboard.pengguna') : route('dashboard.admin');
        $breadCumb = [
            ['title' => 'Dashboard', 'url' => $dashboardRoute, 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index()
    {
        $breadCumb = $this->breadCumb(['url' => 'javascript:void(0);', 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Profil', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Profil - ' . config('app.name'),
            'pageTitle' => "Profil Saya",
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => Auth::user(),
        ];

        return view('admin.pengguna.profil', $data);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        return $this->profileService->update($request);
    }

    public function updatePhoto(UpdatePhotoRequest $request): JsonResponse
    {
        return $this->profileService->updatePhoto($request);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        return $this->profileService->updatePassword($request);
    }

    public function destroy(): JsonResponse
    {
        return $this->profileService->destroy();
    }
}
