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

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Admin Dashboard</h1>

<p>
    Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
</p>
<br><br>

<a href="categories/index.php">Manage Job Categories</a>
<br><br>
<a href="jobs.php">Manage All Jobs</a>
<br><br>
<a href="users.php">Manage Users</a>
<br><br>
<a href="applications.php">View All Applications</a>
<br><br>
<a href="../authentication/logout.php">Logout</a>

</body>
</html>