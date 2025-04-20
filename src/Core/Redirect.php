<?php
namespace App\Core;

class Redirect
{
    /**
     * Redirects to a given URL and terminates the script.
     *
     * @param string $toUrl  The target URL to redirect to (can be relative).
     * @param int    $status HTTP status code for the redirect (default: 301).
     *
     * @return void
     */
    public static function to(string $toUrl, int $status = 301): void
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
