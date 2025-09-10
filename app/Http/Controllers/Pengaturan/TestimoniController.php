<?php

namespace App\Http\Controllers\Pengaturan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;

class TestimoniController extends Controller
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
            ['title' => 'Pengaturan', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }
    public function index()
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Testimoni', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Testimoni - ' . config('app.name'),
            'pageTitle' => 'Testimoni',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return view('admin.pengaturan.testimoni', $data);
    }
}
