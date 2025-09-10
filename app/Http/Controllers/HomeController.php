<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;

class HomeController extends Controller
{
    protected $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Dashboard', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index()
    {
        $breadCumb = $this->breadCumb(['url' => route('dashboard.admin'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Home', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Home - ' . config('app.name'),
            'pageTitle' => config('app.name'),
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return view('admin.home.home-admin', $data);
    }

    public function pengguna()
    {
        $breadCumb = $this->breadCumb(['url' => route('dashboard.pengguna'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Home', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Home - ' . config('app.name'),
            'pageTitle' => config('app.name'),
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return view('admin.home.home-pengguna', $data);
    }
}
