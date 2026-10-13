
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
        jobs.id,
        jobs.title,
        jobs.location,
        jobs.salary,
        jobs.deadline,
        jobs.created_at,
        job_categories.name AS category_name,
        users.name AS employer_name
    FROM jobs
    INNER JOIN job_categories
        ON jobs.category_id = job_categories.id
    INNER JOIN users
        ON jobs.employer_id = users.id
    ORDER BY jobs.id DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage All Jobs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Manage All Jobs</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Job Title</th>
        <th>Category</th>
        <th>Employer</th>
        <th>Location</th>
        <th>Salary</th>
        <th>Deadline</th>
        <th>Posted At</th>
        <th>Action</th>
    </tr>

    <?php if ($result && $result->num_rows > 0) { ?>

        <?php while ($job = $result->fetch_assoc()) { ?>

            <tr>
                <td><?php echo $job["id"]; ?></td>

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
                    <?php echo htmlspecialchars($job["location"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["salary"] ?? "Not specified"); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["deadline"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["created_at"]); ?>
                </td>
                <td>
    <form
        method="POST"
        action="delete_job.php"
        onsubmit="return confirm('Are you sure you want to delete this job?');"
    >
        <input
            type="hidden"
            name="job_id"
            value="<?php echo $job['id']; ?>"
        >

        <button type="submit">Delete Job</button>
    </form>
</td>
            </tr>

        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="9">No jobs have been posted yet.</td>
        </tr>

    <?php } ?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
