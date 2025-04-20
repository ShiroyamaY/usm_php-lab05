<?php

function handleRecipeCreate(): void
{
    $errors = [];
    $formData = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $formData = [
            'title' => $_POST['title'] ?? '',
            'category' => $_POST['category'] ?? '',
            'ingredients' => $_POST['ingredients'] ?? '',
            'description' => $_POST['description'] ?? '',
            'steps' => $_POST['steps'] ?? '',
            'tags' => $_POST['tags'] ?? '',
        ];

        $errors = validateRecipe($formData);

        if (empty($errors)) {
            try {
                $sql = "INSERT INTO recipes (title, category, ingredients, description, steps, tags) 
                        VALUES (:title, :category, :ingredients, :description, :steps, :tags)";

                $recipeId = dbInsert($sql, [
                    ':title' => $formData['title'],
                    ':category' => $formData['category'],
                    ':ingredients' => $formData['ingredients'],
                    ':description' => $formData['description'],
                    ':steps' => $formData['steps'],
                    ':tags' => $formData['tags'],
                ]);

                redirect('/recipe/show?id=' . $recipeId);
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $errors['database'] = 'An error occurred while saving the recipe.';
            }
        }
    }

    $categories = getAllCategories();

    renderLayout('recipe/create', [
        'errors' => $errors,
        'formData' => $formData,
        'categories' => $categories,
    ], 'Add New Recipe');
}