<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;


class Dashboard extends Controller
{
    protected $layout = 'custom';


    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return View::make('Dashboard/Index')->shares('title', 'Dashboard');
    }
}
