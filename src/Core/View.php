<?php

namespace App\Core;

class View
{
    protected static array $data = [];

    /**
     * Sets the data for the template
     *
     * @param array $data
     */
    public static function setData(array $data): void
    {
        self::$data = $data;
    }

    /**
     * Gets the data
     *
     * @param string|null $key
     * @return mixed
     */
    public static function getData(string $key = null): mixed
    {
        if ($key === null) {
            return self::$data;
        }
        return self::$data[$key] ?? null;
    }

    /**
     * Renders the template with the provided data
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public static function render(string $template, array $data = []): string
    {
        ob_start();
        extract($data);
        include __DIR__ . '/../../templates/' . $template . '.php';
        return ob_get_clean();
    }

    /**
     * Renders the template within a layout
     *
     * @param string $template
     * @param array $data
     * @param string $layout
     * @return string
     */
    public static function renderWithLayout(string $template, array $data = [], string $layout = 'default.layout'): string
    {
        $data['content'] = self::render($template, $data);
        return self::render($layout, $data);
    }
}