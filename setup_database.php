<?php
/**
 * Database Setup Script
 * This script will create the database and import the SQL file
 */

// Database configuration (without database name)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "Setting up database...\n";

try {
    // Connect to MySQL server without selecting a database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "Connected to MySQL server.\n";
    
    // Create database if it doesn't exist
    $dbName = 'db_printshop';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "Database '$dbName' created or already exists.\n";
    
    // Select the database
    $pdo->exec("USE `$dbName`");
    echo "Using database '$dbName'.\n";
    
    // Read and execute SQL file
    $sqlFile = __DIR__ . '/db_printshop.sql';
    
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found at $sqlFile\n");
    }
    
    echo "Reading SQL file...\n";
    $sql = file_get_contents($sqlFile);
    
    // Remove the CREATE DATABASE and USE statements since we already handled that
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS.*?;/i', '', $sql);
    $sql = preg_replace('/USE `.*?`;/i', '', $sql);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   !preg_match('/^--/', $stmt) && 
                   !preg_match('/^\/\*/', $stmt) &&
                   !preg_match('/^\s*$/', $stmt);
        }
    );
    
    echo "Executing SQL statements...\n";
    $executed = 0;
    
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            try {
                $pdo->exec($statement);
                $executed++;
            } catch (PDOException $e) {
                // Ignore errors for existing tables/indexes
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Executed $executed SQL statements.\n";
    echo "\nDatabase setup completed successfully!\n";
    echo "You can now use the application.\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>

