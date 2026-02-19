<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Gate;


class DashboardController extends Controller
{
    public function index(){
        // $gates = Gate::abilities();

        // dd($gates);
        return view('dashboard.index');
    }

    public function store(Request $request){

    }

    public function logout(){

    }
}
