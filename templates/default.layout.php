<?php
/**
 * @var string $title
 * @var string $content
 */
?>
<!-- this is main layout -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Recipe Book' ?></title>
    <link rel="stylesheet" href="/assets/styles/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="container">
        <nav>
            <h1 style="margin: 0; font-size: 1.5rem;">Recipe Book</h1>
            <div style="flex-grow: 1;"></div>
            <a href="/">Home</a>
            <a href="/recipe/create">Add Recipe</a>
        </nav>
    </div>
</header>

<main class="container">
    <?php echo $content ?>
</main>

<footer class="container">
    <p>&copy; <?php echo date('Y') ?> Recipe Book. All rights reserved.</p>
</footer>
</body>
</html>