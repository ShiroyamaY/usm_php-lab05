<?php
/**
 * @var array $recipes
 * @var string $pagination
 */
?>
<h2>All Recipes</h2>

<?php if (empty($recipes)): ?>
    <p>No recipes found. <a href="/recipe/create">Add your first recipe</a>.</p>
<?php else: ?>
    <?php foreach ($recipes as $recipe): ?>
        <div class="recipe-card">
            <h3>
                <a href="/recipe/show/<?= $recipe['id'] ?>">
                    <?= sanitize($recipe['title']) ?>
                </a>
            </h3>
            <p><strong>Category:</strong> <?= sanitize($recipe['category_name']) ?></p>
            <p><?= substr(sanitize($recipe['description']), 0, 200) ?>...</p>
            <div>
                <a href="/recipe/edit/<?php echo $recipe['id'] ?>">Edit</a>
                <form method="post" action="/recipe/delete/<?php echo $recipe['id'] ?>">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <?php echo $pagination ?>
<?php endif; ?>