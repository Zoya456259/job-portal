
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

$sql = "
    SELECT
        jobs.id,
        jobs.title,
        jobs.description,
        jobs.location,
        jobs.salary,
        jobs.deadline,
        job_categories.name AS category_name,
        users.name AS employer_name
    FROM jobs
    INNER JOIN job_categories
        ON jobs.category_id = job_categories.id
    INNER JOIN users
        ON jobs.employer_id = users.id
    WHERE jobs.deadline >= CURDATE()
    ORDER BY jobs.created_at DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Jobs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Available Jobs</h1>

<p>Explore jobs posted by employers.</p>

<?php if ($result->num_rows > 0) { ?>

    <table border="1" cellpadding="10">

        <tr>
            <th>Job Title</th>
            <th>Category</th>
            <th>Employer</th>
            <th>Description</th>
            <th>Location</th>
            <th>Salary</th>
            <th>Deadline</th>
            <th>Action</th>
        </tr>

        <?php while ($job = $result->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($job["title"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["category_name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["employer_name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["description"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["location"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["salary"] ?? "Not specified"); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["deadline"]); ?>
                </td>
                <td>
                <a href="apply.php?job_id=<?php echo $job['id']; ?>">
                Apply Now
                </a>
                </td>
            </tr>

        <?php } ?>

    </table>

<?php } else { ?>

    <p>No jobs are currently available.</p>

<?php } ?>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
