<?php
/**
 * Helper functions for application
 */

/**
 * Render a template with data
 *
 * @param string $template Path to template file
 * @param array $data Data to pass to template
 * @return string Rendered template
 */
function render($template, $data = []) {
    ob_start();
    extract($data);
    require __DIR__ . '/../templates/' . $template . '.php';
    return ob_get_clean();
}

/**
 * Render a template inside the layout
 *
 * @param string $template Path to template file
 * @param array $data Data to pass to template
 * @param string $title Page title
 * @return void
 */
function renderLayout($template, $data = [], $title = 'Recipe Book') {
    $content = render($template, $data);
    include __DIR__ . '/../templates/layout.php';
}

/**
 * Redirect to another URL
 *
 * @param string $path Path to redirect to
 * @return void
 */
function redirect($path) {
    header("Location: $path");
    exit;
}

/**
 * Get all categories from database
 *
 * @return array List of categories
 */
function getAllCategories() {
    return dbQuery("SELECT * FROM categories ORDER BY name");
}

/**
 * Validate recipe form data
 *
 * @param array $data Recipe form data
 * @return array Errors array
 */
function validateRecipe($data) {
    $errors = [];

    if (empty($data['title'])) {
        $errors['title'] = 'Title is required';
    } elseif (strlen($data['title']) > 255) {
        $errors['title'] = 'Title must be less than 255 characters';
    }

    if (empty($data['category']) || !is_numeric($data['category'])) {
        $errors['category'] = 'Category is required';
    }

    if (empty($data['ingredients'])) {
        $errors['ingredients'] = 'Ingredients are required';
    }

    if (empty($data['description'])) {
        $errors['description'] = 'Description is required';
    }

    if (empty($data['steps'])) {
        $errors['steps'] = 'Steps are required';
    }

    return $errors;
}

/**
 * Sanitize input data
 *
 * @param mixed $data Data to sanitize
 * @return mixed Sanitized data
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
    } else {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    return $data;
}

/**
 * Get page parameter from URL with default value
 *
 * @param int $default Default page number
 * @return int Current page number
 */
function getCurrentPage($default = 1) {
    return isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : $default;
}

/**
 * Get recipes count
 *
 * @return int Total recipes count
 */
function getRecipesCount() {
    $result = dbQueryOne("SELECT COUNT(*) as count FROM recipes");
    return $result['count'];
}

/**
 * Generate pagination HTML
 *
 * @param int $currentPage Current page
 * @param int $totalPages Total pages
 * @return string Pagination HTML
 */
function generatePagination(int $currentPage, int $totalPages): string
{
    $html = '<div class="pagination">';

    if ($currentPage > 1) {
        $html .= '<a href="?page=' . ($currentPage - 1) . '">&laquo; Previous</a>';
    } else {
        $html .= '<span class="disabled">&laquo; Previous</span>';
    }

    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="current">' . $i . '</span>';
        } else {
            $html .= '<a href="?page=' . $i . '">' . $i . '</a>';
        }
    }

    if ($currentPage < $totalPages) {
        $html .= '<a href="?page=' . ($currentPage + 1) . '">Next &raquo;</a>';
    } else {
        $html .= '<span class="disabled">Next &raquo;</span>';
    }

    $html .= '</div>';
    return $html;
}