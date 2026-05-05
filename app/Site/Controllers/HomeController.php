<?php

namespace App\Site\Controllers;

use Core\Controller;
use Core\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $this->render('Site', 'home/index', ['title' => 'Bem-vindo ao Modelo MVC Genérico!']);
    }
}
