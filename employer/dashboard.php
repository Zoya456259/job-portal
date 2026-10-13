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

?>

<!DOCTYPE html>
<html>
<head>
    <title>Employer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Employer Dashboard</h1>

<p>
    Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
</p>
<br><br>

<a href="jobs/create.php">Post a New Job</a>

<br><br>

<a href="jobs/index.php">Manage My Jobs</a>
<br><br>

<a href="applications.php">View Applications</a>

<br><br>
<a href="../authentication/logout.php">Logout</a>

</body>
</html>