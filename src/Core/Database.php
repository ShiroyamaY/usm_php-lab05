<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Class Database
 *
 * Handles the database connection and provides basic query methods.
 * Implements the Singleton pattern to ensure a single PDO connection instance.
 */
class Database
{
    /**
     * @var Database|null Singleton instance of the Database class.
     */
    private static ?Database $instance = null;

    /**
     * @var PDO The PDO database connection instance.
     */
    private PDO $pdo;

    /**
     * Private constructor to initialize the PDO connection.
     *
     * @param array $config Database configuration:
     *                      [
     *                          'host' => string,
     *                          'name' => string,
     *                          'username' => string,
     *                          'password' => string,
     *                          'charset' => string,
     *                          'options' => array
     *                      ]
     *
     * @throws RuntimeException If connection fails.
     */
    private function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['name'],
            $config['charset'] ?? 'utf8mb4'
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options'] ?? [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
            );
        } catch (PDOException $e) {
            error_log('DB Connection Error: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed.');
        }
    }

    /**
     * Initializes the database connection.
     * Must be called once before calling getInstance().
     *
     * @param array $config Database configuration array.
     */
    public static function init(array $config): void
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
    }

    /**
     * Returns the singleton instance of the Database.
     *
     * @return Database
     * @throws RuntimeException If init() has not been called.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new RuntimeException('Database is not initialized. Call Database::init($config) first.');
        }

        return self::$instance;
    }

    /**
     * Executes a SELECT query and returns all rows.
     *
     * @param string $sql SQL query with placeholders.
     * @param array $params Parameters to bind in the query.
     * @return array Fetched results.
     */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Executes a SELECT query and returns a single row.
     *
     * @param string $sql SQL query with placeholders.
     * @param array $params Parameters to bind in the query.
     * @return mixed|null The first row or null if none found.
     */
    public function selectOne(string $sql, array $params = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Executes an INSERT query and returns the last inserted ID.
     *
     * @param string $sql SQL query with placeholders.
     * @param array $params Parameters to bind in the query.
     * @return int Last inserted ID.
     */
    public function insert(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Executes an UPDATE or DELETE query and returns the number of affected rows.
     *
     * @param string $sql SQL query with placeholders.
     * @param array $params Parameters to bind in the query.
     * @return int Number of affected rows.
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
