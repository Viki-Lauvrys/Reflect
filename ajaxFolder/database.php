<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "Viki@aVocado4405";
$dbname = "project_x";
 
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// Enable special chars, DON'T YOU DARE DELETE THIS LINE !!!
$conn->set_charset('utf8mb4');

?>