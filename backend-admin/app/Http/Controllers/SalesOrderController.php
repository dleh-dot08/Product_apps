<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    /**
     * Display the sales order page.
     */
    public function index()
    {
        return view('data-akurasi.so.index');
    }
}
