<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
        ]);

        \App\Models\Division::create($validated);

        return back()->with('success', 'Divisi berhasil ditambahkan!');
    }
}
