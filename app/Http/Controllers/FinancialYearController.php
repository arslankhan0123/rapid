<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    public function index()
    {
        return view('finalcial_year.index');
    }
    public function ending()
    {
        return view('finalcial_year.ending');
    }
}
