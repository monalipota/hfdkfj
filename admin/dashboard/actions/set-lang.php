<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $language = $_POST['language'];
    $_SESSION['language'] = $language;
}

header("Location: ../");