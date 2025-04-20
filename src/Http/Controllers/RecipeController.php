<?php

namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Redirect;
use App\Core\View;
use PDOException;

class RecipeController
{
    protected Database $db;

    /**
     * RecipeController constructor.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Display a paginated list of recipes.
     *
     * @return string
     */
    public function index(): string
    {
        $page = getCurrentPage();
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $totalRecipes = $this->getRecipesCount();
        $totalPages = ceil($totalRecipes / $limit);

        $sql = "SELECT r.*, c.name as category_name 
                FROM recipes r 
                JOIN categories c ON r.category = c.id 
                ORDER BY r.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $recipes = $this->db->select($sql, [
            ':limit' => $limit,
            ':offset' => $offset
        ]);

        $pagination = generatePagination($page, $totalPages);

        return View::renderWithLayout('recipe/index', [
            'title' => 'Home',
            'recipes' => $recipes,
            'pagination' => $pagination
        ]);
    }

    /**
     * Create a new recipe.
     *
     * @return string
     */
    public function create(): string
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

            $errors = $this->validateRecipe($formData);

            if (empty($errors)) {
                try {
                    $sql = "INSERT INTO recipes (title, category, ingredients, description, steps, tags) 
                        VALUES (:title, :category, :ingredients, :description, :steps, :tags)";

                    $recipeId = $this->db->insert($sql, [
                        ':title' => $formData['title'],
                        ':category' => $formData['category'],
                        ':ingredients' => $formData['ingredients'],
                        ':description' => $formData['description'],
                        ':steps' => $formData['steps'],
                        ':tags' => $formData['tags'],
                    ]);

                    Redirect::to('/recipe/show?id=' . $recipeId);
                } catch (PDOException $e) {
                    error_log("Database error: " . $e->getMessage());
                    $errors['database'] = 'An error occurred while saving the recipe.';
                }
            }
        }

        $categories = $this->db->select("SELECT * FROM categories ORDER BY name");

        return View::renderWithLayout('recipe/create', [
            'title' => 'Add new recipe',
            'errors' => $errors,
            'formData' => $formData,
            'categories' => $categories,
        ]);
    }

    /**
     * Delete a recipe by ID.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        if (!isset($id) || !is_numeric($id)) {
            Redirect::to('/');
        }

        try {
            $sql = "DELETE FROM recipes WHERE id = :id";
            $this->db->execute($sql, [':id' => $id]);

            Redirect::to('/');
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo "An error occurred while deleting the recipe.";
            header("refresh:3;url=/");
            exit;
        }
    }

    /**
     * Edit a recipe by ID.
     *
     * @param int $id
     * @return string
     */
    public function edit(int $id): string
    {
        $errors = [];

        $sql = "SELECT r.*, c.name as category_name 
            FROM recipes r 
            JOIN categories c ON r.category = c.id 
            WHERE r.id = :id";

        $recipe = $this->db->selectOne($sql, [':id' => $id]);

        if (!$recipe) {
            Redirect::to('/');
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

            $errors = $this->validateRecipe($recipe);

            if (empty($errors)) {
                try {
                    $sql = "UPDATE recipes 
                        SET title = :title, category = :category, ingredients = :ingredients, 
                            description = :description, steps = :steps, tags = :tags 
                        WHERE id = :id";

                    $this->db->execute($sql, [
                        ':id' => $id,
                        ':title' => $recipe['title'],
                        ':category' => $recipe['category'],
                        ':ingredients' => $recipe['ingredients'],
                        ':description' => $recipe['description'],
                        ':steps' => $recipe['steps'],
                        ':tags' => $recipe['tags'],
                    ]);

                    Redirect::to('/recipe/show?id=' . $id);
                } catch (PDOException $e) {
                    error_log("Database error: " . $e->getMessage());
                    $errors['database'] = 'An error occurred while updating the recipe.';
                }
            }
        } else {
            $sql = "SELECT * FROM recipes WHERE id = :id";
            $recipe = $this->db->selectOne($sql, [':id' => $id]);
        }

        $categories = $this->getAllCategories();

        return View::renderWithLayout('recipe/edit', [
            'title' => 'Edit Recipe',
            'recipe' => $recipe,
            'errors' => $errors,
            'categories' => $categories,
        ]);
    }

    /**
     * Show recipe details by ID.
     *
     * @param int $id
     * @return string
     */
    public function show($id): string
    {
        $sql = "SELECT r.*, c.name as category_name 
            FROM recipes r 
            JOIN categories c ON r.category = c.id 
            WHERE r.id = :id";

        $recipe = $this->db->selectOne($sql, [':id' => $id]);
        var_dump($recipe);
        if (!$recipe) {
            Redirect::to('/');
        }

        return View::renderWithLayout('recipe/show', [
            'title' => $recipe['title'],
            'recipe' => $recipe,
        ]);
    }

    /**
     * Validate recipe form data
     *
     * @param array $data Recipe form data
     * @return array Errors array
     */
    public function validateRecipe(array $data): array
    {
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
     * Get all categories from database
     *
     * @return array List of categories
     */
    public function getAllCategories(): array
    {
        return $this->db->select("SELECT * FROM categories ORDER BY name");
    }

    /**
     * Get recipes count
     *
     * @return int Total recipes count
     */
    public function getRecipesCount(): int
    {
        $result = $this->db->selectOne("SELECT COUNT(*) as count FROM recipes");
        return $result['count'];
    }
}
