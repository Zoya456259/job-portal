
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../authentication/login.php");
    exit;
}

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$user_id = (int) ($_POST["user_id"] ?? 0);

if ($user_id > 0) {

    $stmt = $conn->prepare(
        "DELETE FROM users
         WHERE id = ? AND role != 'admin'"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt->close();
}

header("Location: users.php");
exit;

?>
