<?php

namespace App\Core;

class Application
{
    public function boot(): void
    {
        Config::load();

        require_once __DIR__ . '/../../routes/web.php';

        Database::init(Config::get('db'));
        Route::dispatch();
    }
}