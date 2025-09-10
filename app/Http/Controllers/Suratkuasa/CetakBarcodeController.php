<?php

namespace App\Http\Controllers\Suratkuasa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CetakBarcodeController extends Controller
{
    public function index()
    {
        $data = ['title' => 'Cetak Barcode Pendaftaran Surat Kuasa'];
        return view('admin.template.pdf-barcode', $data);
    }
}
