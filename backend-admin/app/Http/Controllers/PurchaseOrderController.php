<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * Display the purchase order page.
     */
    public function index()
    {
        return view('data-akurasi.po.index');
    }
}
