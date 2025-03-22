<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Login');
    }
    public function main()
    {
        return Inertia::render('Main');
    }
    public function manageTask()
    {
        return Inertia::render('ManageTask');
    }
    public function register()
    {
        return Inertia::render('Register');
    }
}
