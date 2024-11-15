<?php
global $mysqli;
$mysqli = new mysqli("localhost", "root", "", "techdeal");

if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}


register_shutdown_function(function () use ($mysqli) {
    $mysqli->close();
});