<?php

function handleRecipeShow(): void
{
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        redirect('/');
    }

    $id = (int)$_GET['id'];

    $sql = "SELECT r.*, c.name as category_name 
            FROM recipes r 
            JOIN categories c ON r.category = c.id 
            WHERE r.id = :id";

    $recipe = dbQueryOne($sql, [':id' => $id]);

    if (!$recipe) {
        redirect('/');
    }

    renderLayout('recipe/show', [
        'recipe' => $recipe,
    ], $recipe['title']);
}