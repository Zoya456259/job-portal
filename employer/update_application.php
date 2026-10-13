
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "employer") {
    header("Location: ../authentication/login.php");
    exit;
}

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: applications.php");
    exit;
}

$application_id = (int) ($_POST["application_id"] ?? 0);
$status = $_POST["status"] ?? "";
$employer_id = (int) $_SESSION["user_id"];

if (
    $application_id <= 0 ||
    !in_array($status, ["accepted", "rejected"], true)
) {
    header("Location: applications.php");
    exit;
}

$stmt = $conn->prepare(
    "UPDATE applications
     INNER JOIN jobs ON applications.job_id = jobs.id
     SET applications.status = ?
     WHERE applications.id = ?
     AND jobs.employer_id = ?"
);

$stmt->bind_param(
    "sii",
    $status,
    $application_id,
    $employer_id
);

$stmt->execute();

header("Location: applications.php");
exit;

?>
