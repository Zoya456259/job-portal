<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "job_seeker") {
    header("Location: ../authentication/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Seeker Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Job Seeker Dashboard</h1>

<p>
    Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
</p>
<br><br>

<a href="profile.php">My Profile</a>
<br><br>

<a href="browse_jobs.php">Browse Jobs</a>

<br><br>

<a href="applications.php">My Applications</a>

<br><br>
<a href="../authentication/logout.php">Logout</a>

</body>
</html>