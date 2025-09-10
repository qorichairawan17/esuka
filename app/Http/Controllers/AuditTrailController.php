<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
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
        $breadCumb[] =  ['title' => 'Audit Trail', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Audit Trail - ' . config('app.name'),
            'pageTitle' => 'Audit Trail',
            'breadCumb' => $breadCumb
        ];

        return view('admin.audit-trail.data-audit-trail', $data);
    }
}
