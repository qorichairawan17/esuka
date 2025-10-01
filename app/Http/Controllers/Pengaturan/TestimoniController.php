<?php

namespace App\Http\Controllers\Pengaturan;

use Illuminate\Http\Request;
use App\Services\TestimoniService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\DataTables\TestimoniDataTable;
use App\Models\Pengaturan\AplikasiModel;

class TestimoniController extends Controller
{
    protected $infoApp, $testimoniService;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
        $this->testimoniService = new TestimoniService();
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Pengaturan', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }

    public function index(TestimoniDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Testimoni', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Testimoni - ' . config('app.name'),
            'pageTitle' => 'Testimoni',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.pengaturan.testimoni', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'pesan' => 'required|string|max:500',
        ]);
        return $this->testimoniService->store($validated);
    }

    public function edit(Request $request)
    {
        return $this->testimoniService->edit($request);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'pesan' => 'required|string|max:500',
            'publish' => 'nullable|boolean',
        ]);
        return $this->testimoniService->update($validated, $id);
    }
}
