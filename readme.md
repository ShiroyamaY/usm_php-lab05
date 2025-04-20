# Лабораторная работа №5. Работа с базой данных

## Цель работы

Освоить архитектуру с единой точкой входа, подключение шаблонов для визуализации страниц, а также переход от хранения данных в файле к использованию базы данных (MySQL).

## Условия

Продолжите разработку проекта, начатого в предыдущей лабораторной работе, необходимо:

- Реализовать архитектуру с единой точкой входа (`index.php`), обрабатывающей все входящие HTTP-запросы.
- Настроить базовую систему шаблонов с использованием файла `layout.php` и отдельных представлений для разных страниц.
- Перенести логику работы с рецептами из файловой системы в базу данных (например, `MySQL`).

### Примечания к лабораторной номер 5

Класс конфига был взят с проекта который писался на лабораторных работах, 
в остальном было написанно самостоятельно в рамках данной работы(весьма халатно)

---

### Core

---

### **Application** 
- класс отвечающий за сборку приложения, метод **boot**

> 1. Загружает конфиг
> 2. Init Database, с необходимой конфигурацией
> 3. Route Dispatch для обработки необходимого рута

[Application](src/Core/Application.php)

---

### **Database**
> класс с основными необходимыми методами для работы с базой данных, для небольшой "инкапсуляции" работы с бд

[Database](src/Core/Database.php)

---
### **Route** 

> Был создан небольшой роутер для маршрутизации запросов в контроллеры:

[Route](src/Core/Route.php)

--- 
### **Redirect**

> Класс для редиректов

[Redirect](src/Core/Redirect.php)

---

### **View**

> Небольшой класс фасад для рендера темлейтов

- **render** - рендерит без layout 
- **renderWithLayout** - рендерит с layout

и так же можно передавать данные как в методах рендера так и при помощи **get/setData**

[View](src/Core/View.php)

---

### Темплейты

> Темплейты находятся в соответвующей папке **templates**
> Все темлейты круд операций называются соответствующе и хранятся в папке с названием соответствующе названию сущности 

---

### Конфиг базы данных

> Хранится в .env и подгружается в код за счет пакета: vlucas/phpdotenv

```php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();
```

> Далее используем $_ENV для доступа к загруженным переменным
- db.php
```php
<?php

return [
    'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
    'host'     => $_ENV['MYSQL_HOST']     ?? '127.0.0.1',
    'port'     => $_ENV['MYSQL_PORT']     ?? '3306',
    'name'   => $_ENV['MYSQL_DATABASE'] ?? 'db',
    'username' => $_ENV['MYSQL_USER']     ?? 'user',
    'password' => $_ENV['MYSQL_PASSWORD'] ?? 'password',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
```

### Миграции для миграций используется пакет phinx:

Пример использования phinx:

#### Phinx

- Создаем стандартный файл конфигурации, и указываем настройки соответсвующие локальному окружению
```bash
vendor/bin/phinx init
```

1. Создание миграции
```
vendor/bin/phinx create MigrationName
```

2. Запуск миграции
```
vendor/bin/phinx migrate
```

3. Откат миграции

```
vendor/bin/phinx rollback

vendor/bin/phinx rollback -t <version>
```

4. Проверка статуса миграций(phinxlog таблица):

```
vendor/bin/phinx status
```

5. Refresh миграций
```
vendor/bin/phinx rollback -t 0 && vendor/bin/phinx migrate
```

### **CRUD Операции в `RecipeController`**

[RecipeController](src/Http/Controllers/RecipeController.php)

Контроллер `RecipeController` реализует основные CRUD-операции (создание, чтение, обновление, удаление) для сущности "Recipe".

---

#### **index()**
Метод для отображения списка рецептов с пагинацией.

> 1. Получает текущую страницу.
> 2. Вычисляет общее количество страниц.
> 3. Загружает рецепты с учетом LIMIT и OFFSET.
> 4. Рендерит шаблон `recipe/index` с данными рецептов и пагинацией.

---

#### **create()**
Метод для создания нового рецепта.

> 1. При GET-запросе отображает форму создания.
> 2. При POST-запросе валидирует входные данные.
> 3. При успешной валидации сохраняет рецепт в базу данных.
> 4. Перенаправляет на просмотр созданного рецепта.
> 5. При ошибках отображает их в форме.

```php
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
```
---

#### **edit($id)**
Метод для редактирования существующего рецепта.

> 1. Загружает данные рецепта по `id`.
> 2. При POST-запросе валидирует и обновляет данные.
> 3. Сохраняет изменения в базе и перенаправляет на просмотр.
> 4. При GET-запросе отображает форму редактирования с текущими значениями.

```php
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
}
```
---

#### **delete($id)**
Метод для удаления рецепта по `id`.

> 1. Проверяет валидность `id`.
> 2. Выполняет SQL-запрос на удаление.
> 3. После удаления — редирект на главную страницу.

```php 
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
```
---

#### **show($id)**
Метод для отображения одного рецепта.

> 1. Получает данные рецепта по `id`.
> 2. Если рецепт найден — отображает шаблон `recipe/show`.
> 3. В противном случае — редирект на главную.

```php 
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
```

---

Каждая из операций использует `Database` для взаимодействия с БД, `View` для рендера шаблонов, и `Redirect` для редиректов.

---

### Для запуска, используется docker, а именно контейнеры для mysql, nginx, php.

---

## Ответы на вопросы

---

**1. Какие преимущества даёт использование единой точки входа в веб-приложении?**  

> Одна точка входа (например, `index.php`) позволяет централизованно обрабатывать все запросы. Это упрощает маршрутизацию, безопасность и подключение нужных компонентов (БД, сессии и т. д.).

---

**2. Какие преимущества даёт использование шаблонов?**  

> Шаблоны помогают разделить логику и отображение. Код становится чище, легче читать и поддерживать. Удобно переиспользовать общие части (например, шапку и подвал).

---

**3. Какие преимущества даёт хранение данных в базе по сравнению с файлами?**  

> Базы быстрее работают с большим объёмом данных, умеют делать выборки, фильтрацию, поддерживают связи между таблицами. Файлы проще, но база — удобнее и надёжнее.

---

**4. Что такое SQL-инъекция? Пример и защита.**  
> Это когда в SQL-запрос вставляют вредоносный код. Пример:

```php
$sql = "SELECT * FROM users WHERE login = '$login' AND pass = '$pass'";
```

Если ввести `pass = ' OR 1=1 --`, можно войти без пароля.  
Чтобы избежать — надо использовать подготовленные запросы (`prepare()`), экранировать данные и не писать SQL вручную с данными от пользователя.

--- 

