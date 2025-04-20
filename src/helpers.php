<?php
/**
 * Helper functions for application
 */

/**
 * Sanitize input data
 *
 * @param mixed $data Data to sanitize
 * @return mixed Sanitized data
 */
function sanitize(mixed $data): mixed
{
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
function getCurrentPage(int $default = 1): int
{
    return isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : $default;
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