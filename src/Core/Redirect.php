<?php
namespace App\Core;

use JetBrains\PhpStorm\NoReturn;

class Redirect
{
    #[NoReturn] public static function to(string $toUrl, int $status = 301): void
    {
        if (!str_starts_with($toUrl, '/')) {
            $toUrl = '/' . $toUrl;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'];

        $location = $scheme . '://' . $host . $toUrl;

        header('Location: ' . $location, true, $status);
        exit();
    }
}
