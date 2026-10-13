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

$id = (int) ($_GET["id"] ?? 0);
$employer_id = (int) $_SESSION["user_id"];

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Get this employer's job only
$stmt = $conn->prepare(
    "SELECT * FROM jobs WHERE id = ? AND employer_id = ?"
);

$stmt->bind_param("ii", $id, $employer_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: index.php");
    exit;
}

$job = $result->fetch_assoc();

$categories = $conn->query(
    "SELECT id, name FROM job_categories ORDER BY name ASC"
);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $location = trim($_POST["location"]);
    $salary = trim($_POST["salary"]);
    $deadline = $_POST["deadline"] ?? "";
    $category_id = (int) ($_POST["category_id"] ?? 0);

    if (
        empty($title) ||
        empty($description) ||
        empty($location) ||
        empty($deadline) ||
        $category_id <= 0
    ) {
        $message = "Please fill in all required fields.";
    } else {

        $update = $conn->prepare(
            "UPDATE jobs
             SET category_id = ?, title = ?, description = ?,
                 location = ?, salary = ?, deadline = ?
             WHERE id = ? AND employer_id = ?"
        );

        $update->bind_param(
            "isssssii",
            $category_id,
            $title,
            $description,
            $location,
            $salary,
            $deadline,
            $id,
            $employer_id
        );

        if ($update->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $message = "Failed to update job.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Job</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<h1>Edit Job</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

    <label>Job Title *</label><br>
    <input
        type="text"
        name="title"
        value="<?php echo htmlspecialchars($job["title"]); ?>"
        required
    >
    <br><br>

    <label>Job Category *</label><br>

    <select name="category_id" required>
        <option value="">Select Category</option>

        <?php while ($category = $categories->fetch_assoc()) { ?>

            <option
                value="<?php echo $category["id"]; ?>"
                <?php
                echo $category["id"] == $job["category_id"]
                    ? "selected"
                    : "";
                ?>
            >
                <?php echo htmlspecialchars($category["name"]); ?>
            </option>

        <?php } ?>

    </select>

    <br><br>

    <label>Job Description *</label><br>
    <textarea name="description" required><?php
        echo htmlspecialchars($job["description"]);
    ?></textarea>
    <br><br>

    <label>Location *</label><br>
    <input
        type="text"
        name="location"
        value="<?php echo htmlspecialchars($job["location"]); ?>"
        required
    >
    <br><br>

    <label>Salary</label><br>
    <input
        type="text"
        name="salary"
        value="<?php echo htmlspecialchars($job["salary"] ?? ""); ?>"
    >
    <br><br>

    <label>Application Deadline *</label><br>
    <input
        type="date"
        name="deadline"
        value="<?php echo htmlspecialchars($job["deadline"]); ?>"
        required
    >
    <br><br>

    <button type="submit">Update Job</button>

</form>

<br>

<a href="index.php">Back to My Jobs</a>

</body>
</html>