<?php
/**
 * @var array $errors
 * @var array $formData
 * @var array $categories
 */
?>
<h2>Add New Recipe</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <p>Please fix the following errors:</p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/recipe/create">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo isset($formData['title']) ? sanitize($formData['title']) : '' ?>">
        <?php if (isset($errors['title'])): ?>
            <div class="error"><?php echo $errors['title'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id'] ?>" <?php echo isset($formData['category']) && $formData['category'] == $category['id'] ? 'selected' : '' ?>>
                    <?php echo sanitize($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category'])): ?>
            <div class="error"><?php echo $errors['category'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="ingredients">Ingredients (one per line)</label>
        <textarea id="ingredients" name="ingredients" rows="5"><?= isset($formData['ingredients']) ? sanitize($formData['ingredients']) : '' ?></textarea>
        <?php if (isset($errors['ingredients'])): ?>
            <div class="error"><?= $errors['ingredients'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3"><?php echo isset($formData['description']) ? sanitize($formData['description']) : '' ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <div class="error"><?php echo $errors['description'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="steps">Steps (one per line)</label>
        <textarea id="steps" name="steps" rows="5"><?php echo isset($formData['steps']) ? sanitize($formData['steps']) : '' ?></textarea>
        <?php if (isset($errors['steps'])): ?>
            <div class="error"><?php echo $errors['steps'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="tags">Tags (comma separated)</label>
        <input type="text" id="tags" name="tags" value="<?php echo isset($formData['tags']) ? sanitize($formData['tags']) : '' ?>">
    </div>

    <button type="submit">Add Recipe</button>
</form>