
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

$sql = "
    SELECT
        applications.id,
        applications.cover_letter,
        applications.status,
        applications.applied_at,
        jobs.title AS job_title,
        seeker.name AS applicant_name,
        seeker.email AS applicant_email,
        employer.name AS employer_name
    FROM applications
    INNER JOIN jobs
        ON applications.job_id = jobs.id
    INNER JOIN users AS seeker
        ON applications.job_seeker_id = seeker.id
    INNER JOIN users AS employer
        ON jobs.employer_id = employer.id
    ORDER BY applications.applied_at DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>All Applications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>All Job Applications</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>Job Title</th>
        <th>Applicant</th>
        <th>Applicant Email</th>
        <th>Employer</th>
        <th>Cover Letter</th>
        <th>Status</th>
        <th>Applied At</th>
    </tr>

    <?php if ($result && $result->num_rows > 0) { ?>

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
                    <?php echo htmlspecialchars($application["employer_name"]); ?>
                </td>

                <td>
                    <?php echo nl2br(htmlspecialchars($application["cover_letter"])); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars(ucfirst($application["status"])); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($application["applied_at"]); ?>
                </td>
            </tr>

        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="7">No applications found.</td>
        </tr>

    <?php } ?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
