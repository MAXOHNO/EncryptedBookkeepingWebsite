<?php
$host = "localhost";
$name = "books_database";
$user = "root";
$password = "root";
try {
    $mysql = new PDO("mysql:host=$host;dbname=$name", $user, $password);
    
} catch (PDOException $e) {
    echo "SQL Error: " .$e->getMessage();
}

?>