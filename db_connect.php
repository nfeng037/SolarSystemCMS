<?php
$host = 'localhost'; // Host name
$db   = 'SolarSystemCMS'; // Database name
$user = 'root'; // Database user
$pass = ''; // Database password
$charset = 'utf8mb4'; // Database character set

// Set up DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Set up options (to throw exceptions on errors)
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create a PDO instance (connect to the database)
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Handle any errors by throwing an exception with the error message
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

?>