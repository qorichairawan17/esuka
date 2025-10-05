<?php

namespace App\Http\Controllers\Panduan;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use App\Models\Pengaturan\AplikasiModel;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\MissingDependencyException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PanduanController extends Controller
{
    protected $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::remember('infoApp', now()->addMinutes(60), function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($slug)
    {
        $parts = explode('/', $slug);
        $breadcrumbs = [['title' => 'Panduan', 'url' => route('panduan.show'), 'active' => '', 'aria' => '']];
        $currentUrl = '';

        foreach ($parts as $index => $part) {
            if ($part === 'home' && $index === 0) {
                $breadcrumbs[] = ['title' => 'Selamat Datang', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
                continue;
            }

            $currentUrl .= ($currentUrl ? '/' : '') . $part;
            $title = Str::ucfirst(str_replace('-', ' ', $part));
            $isLast = $index === count($parts) - 1;

            $breadcrumbs[] = [
                'title' => $title,
                'url' => $isLast ? 'javascript:void(0);' : route('panduan.show', $currentUrl),
                'active' => $isLast ? 'active' : '',
                'aria' => $isLast ? 'aria-current="page"' : '',
            ];
        }

        return $breadcrumbs;
    }

    public function show($slug = 'home')
    {
        $path = resource_path('/views/admin/panduan/resources/' . str_replace('/', DIRECTORY_SEPARATOR, $slug) . '.md');

        if (!File::exists($path)) {
            throw new NotFoundHttpException('Halaman panduan tidak ditemukan.');
        }

        try {
            // $converter = new CommonMarkConverter(['html_input' => 'escape', 'allow_unsafe_links' => false]);
            $converter = new CommonMarkConverter(['allow_unsafe_links' => false]);
            $markdownContent = File::get($path);
            // Render blade directives inside markdown
            $renderedContent = Blade::render($markdownContent, [
                'infoApp' => $this->infoApp
            ]);
            $htmlContent = $converter->convert($renderedContent);
        } catch (MissingDependencyException $e) {
            throw new \Exception("Pastikan Anda sudah menjalankan 'composer require league/commonmark'", 500, $e);
        }

        // Get the last part of slug for title
        $slugParts = explode('/', $slug);
        $lastSlug = end($slugParts);
        $convertTitle = $lastSlug === 'home' ? 'Panduan Penggunaan' : Str::title(str_replace('-', ' ', $lastSlug));

        $data = [
            'title' => $convertTitle . ' - ' . config('app.name'),
            'pageTitle' => config('app.name'),
            'breadCumb' => $this->breadCumb($slug),
            'infoApp' => $this->infoApp,
            'content' => $htmlContent,
            'slug' => $slug,
        ];

        return view('admin.panduan.pages.show', $data);
    }
}
