<?php
function handleRecipeDelete(): void
{
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        redirect('/');
    }

    $id = (int)$_GET['id'];

    try {
        $sql = "DELETE FROM recipes WHERE id = :id";
        dbExecute($sql, [':id' => $id]);

        redirect('/');
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo "An error occurred while deleting the recipe.";
        header("refresh:3;url=/");
        exit;
    }
}