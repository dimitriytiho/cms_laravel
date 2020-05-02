<?php

namespace App\Modules\Auth\Controllers;

use App\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class HomeController extends AppController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->class);
        App::set('c', $c);
        View::share(compact('class', 'c', 'view'));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view("{$this->viewPathModule}.{$this->view}");
    }
}
