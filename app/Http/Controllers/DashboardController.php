<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard', ['title' => 'Admin Dashboard']);
    }

    public function seller()
    {
        $categories = Category::all();
        return view('seller.dashboard', ['title' => 'Seller Dashboard'], compact('categories'));
    }

    public function buyer()
    {
        return view('buyer.dashboard', ['title' => 'Buyer Dashboard']);
    }
}
