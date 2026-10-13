
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "employer") {
    header("Location: ../../authentication/login.php");
    exit;
}

require_once "../../config/database.php";

$id = (int) ($_GET["id"] ?? 0);
$employer_id = (int) $_SESSION["user_id"];

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare(
    "DELETE FROM jobs WHERE id = ? AND employer_id = ?"
);

$stmt->bind_param("ii", $id, $employer_id);
$stmt->execute();

header("Location: index.php");
exit;

?>
