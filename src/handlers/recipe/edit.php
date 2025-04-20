<?php

function handleRecipeEdit(): void
{
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        redirect('/');
    }

    $id = (int)$_GET['id'];
    $errors = [];

    $sql = "SELECT r.*, c.name as category_name 
            FROM recipes r 
            JOIN categories c ON r.category = c.id 
            WHERE r.id = :id";

    $recipe = dbQueryOne($sql, [':id' => $id]);

    if (!$recipe) {
        redirect('/');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $recipe = [
            'id' => $id,
            'title' => $_POST['title'] ?? '',
            'category' => $_POST['category'] ?? '',
            'ingredients' => $_POST['ingredients'] ?? '',
            'description' => $_POST['description'] ?? '',
            'steps' => $_POST['steps'] ?? '',
            'tags' => $_POST['tags'] ?? '',
        ];

        $errors = validateRecipe($recipe);

        if (empty($errors)) {
            try {
                $sql = "UPDATE recipes 
                        SET title = :title, category = :category, ingredients = :ingredients, 
                            description = :description, steps = :steps, tags = :tags 
                        WHERE id = :id";

                dbExecute($sql, [
                    ':id' => $id,
                    ':title' => $recipe['title'],
                    ':category' => $recipe['category'],
                    ':ingredients' => $recipe['ingredients'],
                    ':description' => $recipe['description'],
                    ':steps' => $recipe['steps'],
                    ':tags' => $recipe['tags'],
                ]);

                redirect('/recipe/show?id=' . $id);
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $errors['database'] = 'An error occurred while updating the recipe.';
            }
        }
    } else {
        $sql = "SELECT * FROM recipes WHERE id = :id";
        $recipe = dbQueryOne($sql, [':id' => $id]);
    }

    $categories = getAllCategories();

    renderLayout('recipe/edit', [
        'recipe' => $recipe,
        'errors' => $errors,
        'categories' => $categories,
    ], 'Edit Recipe');
}