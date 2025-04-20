<?php
/**
 * @var array $recipe
 */
?>
<div class="recipe-details">
    <h2><?= sanitize($recipe['title']) ?></h2>
    <p><strong>Category:</strong> <?= sanitize($recipe['category_name']) ?></p>

    <h3>Description</h3>
    <p><?= nl2br(sanitize($recipe['description'])) ?></p>

    <h3>Ingredients</h3>
    <ul>
        <?php foreach (explode("\n", $recipe['ingredients']) as $ingredient): ?>
            <?php if (trim($ingredient)): ?>
                <li><?php echo sanitize(trim($ingredient)) ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <h3>Steps</h3>
    <ol>
        <?php foreach (explode("\n", $recipe['steps']) as $step): ?>
            <?php if (trim($step)): ?>
                <li><?php echo sanitize(trim($step)) ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>

    <?php if (!empty($recipe['tags'])): ?>
        <h3>Tags</h3>
        <div class="tags">
            <?php foreach (explode(',', $recipe['tags']) as $tag): ?>
                <?php if (trim($tag)): ?>
                    <span class="tag"><?php echo sanitize(trim($tag)) ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="actions">
        <a href="/recipe/edit/<?php echo $recipe['id'] ?>">Edit</a>
        <form method="post" action="/recipe/delete/<?php echo $recipe['id'] ?>">
            <button type="submit" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</button>
        </form>
        <a href="/">Back to all recipes</a>
    </div>
</div>