<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function sub()
    {
        $products = \App\Models\Product::getSubscription(5);
        return view('emails.sub.list', compact('products'));
    }

    public function form()
    {
        return view('test.show');
    }

    public function store(Request $request)
    {
        dd($request->all());
    }
}
