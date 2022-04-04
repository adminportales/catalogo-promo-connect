<?php

namespace App\Http\Controllers;

use App\Models\FailedJobsCron;
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
        if (auth()->user()->hasRole('admin')) {
            return redirect()->action([HomeController::class, 'dashboard']);
        } else {
            return redirect('catalogo');
        }
    }

    public function dashboard()
    {
        $erroresPV = FailedJobsCron::where('type',1);
        $erroresWC = FailedJobsCron::where('type',2);
        return view('home', compact('erroresPV', 'erroresWC'));
    }
    public function catalogo()
    {
        return view('cotizador.catalogo');
    }
}
