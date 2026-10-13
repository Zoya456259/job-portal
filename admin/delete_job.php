
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
    header("Location: jobs.php");
    exit;
}

$job_id = (int) ($_POST["job_id"] ?? 0);

if ($job_id > 0) {

    $stmt = $conn->prepare(
        "DELETE FROM jobs WHERE id = ?"
    );

    $stmt->bind_param("i", $job_id);
    $stmt->execute();

    $stmt->close();
}

header("Location: jobs.php");
exit;

?>
