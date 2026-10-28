<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/calendar');
            return;
        }

        $this->render('home.index', [
            'title' => 'Dischedule - Discord-Integrated Schedule & Event Platform'
        ], 'main');
    }
}
