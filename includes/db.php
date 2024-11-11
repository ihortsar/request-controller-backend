<?php
try {
    $db = new Database();
    return $db->getConnection();
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
