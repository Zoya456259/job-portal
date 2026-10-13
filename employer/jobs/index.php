
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "employer") {
    header("Location: ../../authentication/login.php");
    exit;
}

require_once "../../config/database.php";

$employer_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT jobs.*, job_categories.name AS category_name
     FROM jobs
     INNER JOIN job_categories
     ON jobs.category_id = job_categories.id
     WHERE jobs.employer_id = ?
     ORDER BY jobs.id DESC"
);

$stmt->bind_param("i", $employer_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Jobs</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<h1>My Posted Jobs</h1>

<a href="create.php">Create New Job</a>

<br><br>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Job Title</th>
        <th>Category</th>
        <th>Location</th>
        <th>Salary</th>
        <th>Deadline</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows > 0) { ?>

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
                    <?php echo htmlspecialchars($job["location"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($job["salary"] ?? "Not specified"); ?>
                </td>

                <td><?php echo htmlspecialchars($job["deadline"]); ?></td>

                <td>
    <a href="edit.php?id=<?php echo $job['id']; ?>">
        Edit
    </a>

    |

    <a
        href="delete.php?id=<?php echo $job['id']; ?>"
        onclick="return confirm('Are you sure you want to delete this job?');"
    >
        Delete
    </a>
</td>
            </tr>

        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="6">You have not posted any jobs yet.</td>
        </tr>

    <?php } ?>

</table>

<br>

<a href="../dashboard.php">Back to Dashboard</a>

</body>
</html>
