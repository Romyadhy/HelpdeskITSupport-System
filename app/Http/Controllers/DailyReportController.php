<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index(){
        return view('frontend.Report.daily');
    }
    
}
