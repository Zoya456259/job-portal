
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

$employer_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT
        applications.id,
        applications.cover_letter,
        applications.status,
        applications.applied_at,
        jobs.title AS job_title,
        users.name AS applicant_name,
        users.email AS applicant_email
     FROM applications
     INNER JOIN jobs
        ON applications.job_id = jobs.id
     INNER JOIN users
        ON applications.job_seeker_id = users.id
     WHERE jobs.employer_id = ?
     ORDER BY applications.applied_at DESC"
);

$stmt->bind_param("i", $employer_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Applications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Applications for My Jobs</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>Job Title</th>
        <th>Applicant Name</th>
        <th>Applicant Email</th>
        <th>Cover Letter</th>
        <th>Status</th>
        <th>Applied at</th>
        <th>Action</th>
    </tr>

    <?php if ($result->num_rows > 0) { ?>

        <?php while ($application = $result->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($application["job_title"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["applicant_name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["applicant_email"]); ?>
                </td>

                <td>
                    <?php echo nl2br(htmlspecialchars($application["cover_letter"])); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["status"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["applied_at"]); ?>
                </td>
                <td>
    <form method="POST" action="update_application.php">

        <input
            type="hidden"
            name="application_id"
            value="<?php echo $application['id']; ?>"
        >

        <select name="status" required>
            <option value="accepted">Accept</option>
            <option value="rejected">Reject</option>
        </select>

        <button type="submit">Update Status</button>

    </form>
</td>
            </tr>

        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="6">No applications received yet.</td>
        </tr>

    <?php } ?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
