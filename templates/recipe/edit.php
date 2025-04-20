<?php
/**
 * @var array $recipe
 * @var array $errors
 * @var array $categories
 */
?>
<h2>Edit Recipe</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <p>Please fix the following errors:</p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/recipe/edit/<?= $recipe['id'] ?>">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= sanitize($recipe['title']) ?>">
        <?php if (isset($errors['title'])): ?>
            <div class="error"><?= $errors['title'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $recipe['category'] == $category['id'] ? 'selected' : '' ?>>
                    <?= sanitize($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category'])): ?>
            <div class="error"><?= $errors['category'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="ingredients">Ingredients (one per line)</label>
        <textarea id="ingredients" name="ingredients" rows="5"><?= sanitize($recipe['ingredients']) ?></textarea>
        <?php if (isset($errors['ingredients'])): ?>
            <div class="error"><?= $errors['ingredients'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3"><?= sanitize($recipe['description']) ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <div class="error"><?= $errors['description'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="steps">Steps (one per line)</label>
        <textarea id="steps" name="steps" rows="5"><?= sanitize($recipe['steps']) ?></textarea>
        <?php if (isset($errors['steps'])): ?>
            <div class="error"><?= $errors['steps'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="tags">Tags (comma separated)</label>
        <input type="text" id="tags" name="tags" value="<?= sanitize($recipe['tags']) ?>">
    </div>

    <button type="submit">Update Recipe</button>
</form>