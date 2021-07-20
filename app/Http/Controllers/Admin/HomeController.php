<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;


class HomeController extends Controller

{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = Products::all();
        return view('admin.home',compact('products'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function orders()
    {
        $orders = Orders::orderBy('created_at', 'desc')->get();
        return view('admin.orders',compact('orders'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function config()
    {
        $text = Config::first();
        $text = $text->body;
        return view('admin.config',compact('text'));
    }

    /**
     * Edit
     *
     */
    public function configEdit(Request $request)
    {
        $edit = Config::first();
        $edit->body = $request->text;
        $edit->save();
        return redirect()->back()->with('success', 'Update');
    }
}
