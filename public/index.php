<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

require_once __DIR__ . '/../src/handlers/recipe/create.php';
require_once __DIR__ . '/../src/handlers/recipe/edit.php';
require_once __DIR__ . '/../src/handlers/recipe/delete.php';
require_once __DIR__ . '/../src/handlers/recipe/show.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/':
    case '/index.php':
        $page = getCurrentPage();
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $totalRecipes = getRecipesCount();
        $totalPages = ceil($totalRecipes / $limit);

        $sql = "SELECT r.*, c.name as category_name 
                FROM recipes r 
                JOIN categories c ON r.category = c.id 
                ORDER BY r.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $recipes = dbQuery($sql, [
            ':limit' => $limit,
            ':offset' => $offset
        ]);

        $pagination = generatePagination($page, $totalPages);

        renderLayout('index', [
            'recipes' => $recipes,
            'pagination' => $pagination
        ]);
        break;

    case '/recipe/create':
        handleRecipeCreate();
        break;

    case '/recipe/edit':
        handleRecipeEdit();
        break;

    case '/recipe/delete':
        handleRecipeDelete();
        break;

    case '/recipe/show':
        handleRecipeShow();
        break;

    default:
        header('HTTP/1.1 404');
        break;
}