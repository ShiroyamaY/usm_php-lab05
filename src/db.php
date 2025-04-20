<?php
/**
 * Database connection handler
 *
 * @return PDO Instance of PDO connection
 */
function getDbConnection(): PDO
{
    static $pdo;

    if (!$pdo) {
        try {
            $config = require __DIR__ . '/../config/db.php';

            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            exit('Database connection failed. Please contact the administrator.');
        }
    }

    return $pdo;
}

/**
 * Execute a SQL query and return results
 *
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters for the SQL query
 * @return array Results of the query
 */
function dbQuery(string $sql, array $params = []): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Execute a SQL query and return a single row
 *
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters for the SQL query
 * @return array|false Single result row or false
 */
function dbQueryOne(string $sql, array $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * Execute an INSERT query and return last inserted ID
 *
 * @param string $sql SQL insert query with placeholders
 * @param array $params Parameters for the SQL query
 * @return int Last inserted ID
 */
function dbInsert(string $sql, array $params = []): int
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $pdo->lastInsertId();
}

/**
 * Execute an UPDATE or DELETE query and return affected rows
 *
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters for the SQL query
 * @return int Number of affected rows
 */
function dbExecute(string $sql, array $params = []): int
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}