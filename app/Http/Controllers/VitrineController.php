<?php

namespace App\Http\Controllers;

use App\Models\VitrineSetting;
use Illuminate\View\View;

class VitrineController extends Controller
{
    public function home(): View
    {
        $s = VitrineSetting::getGroup('home');

        return view('web.home', compact('s'));
    }

    public function about(): View
    {
        $s = VitrineSetting::getGroup('about');

        return view('web.about', compact('s'));
    }
}
