<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../../authentication/login.php");
    exit;
}

require_once "../../config/database.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = $_GET["id"];

$stmt = $conn->prepare(
    "DELETE FROM job_categories WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

header("Location: index.php");
exit;

?>