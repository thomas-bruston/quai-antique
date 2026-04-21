<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;


class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', []);
    }

    public function mentions(): void
    {
        $this->render('home/mentions', []);
    }
}
