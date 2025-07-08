<?php

$host = 'localhost'; // or the database host
$db = 'muflixpw_pesv2';
$user = 'muflixpw_muflixpw_pesv2';
$pass = 'Aadhya1@aA';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
