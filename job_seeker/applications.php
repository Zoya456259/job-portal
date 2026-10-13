
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

require_once "../config/database.php";

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT
        applications.id,
        applications.status,
        applications.applied_at,
        jobs.title AS job_title,
        jobs.location,
        jobs.salary
     FROM applications
     INNER JOIN jobs ON applications.job_id = jobs.id
     WHERE applications.job_seeker_id = ?
     ORDER BY applications.applied_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Applications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>My Job Applications</h1>

<?php if ($result->num_rows > 0) { ?>

    <table border="1" cellpadding="10">

        <tr>
            <th>Job Title</th>
            <th>Location</th>
            <th>Salary</th>
            <th>Status</th>
            <th>Applied At</th>
        </tr>

        <?php while ($application = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($application["job_title"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["location"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["salary"] ?? "Not specified"); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars(ucfirst($application["status"])); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["applied_at"]); ?>
                </td>

            </tr>

        <?php } ?>

    </table>

<?php } else { ?>

    <p>You have not applied for any jobs yet.</p>

<?php } ?>

<br>

<a href="browse_jobs.php">Browse Jobs</a>

<br><br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
