
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

$message = "";

$categories = $conn->query(
    "SELECT id, name FROM job_categories ORDER BY name ASC"
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $location = trim($_POST["location"]);
    $salary = trim($_POST["salary"]);
    $deadline = $_POST["deadline"];
    $category_id = (int) $_POST["category_id"];
    $employer_id = (int) $_SESSION["user_id"];

    if (
        empty($title) ||
        empty($description) ||
        empty($location) ||
        empty($deadline) ||
        $category_id <= 0
    ) {
        $message = "Please fill in all required fields.";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO jobs
            (employer_id, category_id, title, description, location, salary, deadline)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iisssss",
            $employer_id,
            $category_id,
            $title,
            $description,
            $location,
            $salary,
            $deadline
        );

        if ($stmt->execute()) {
            $message = "Job created successfully!";
        } else {
            $message = "Failed to create job.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Job</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<h1>Create a Job</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

    <label>Job Title *</label><br>
    <input type="text" name="title" required>
    <br><br>

    <label>Job Category *</label><br>
    <select name="category_id" required>
        <option value="">Select Category</option>

        <?php while ($category = $categories->fetch_assoc()) { ?>
            <option value="<?php echo $category["id"]; ?>">
                <?php echo htmlspecialchars($category["name"]); ?>
            </option>
        <?php } ?>

    </select>
    <br><br>

    <label>Job Description *</label><br>
    <textarea name="description" required></textarea>
    <br><br>

    <label>Location *</label><br>
    <input type="text" name="location" required>
    <br><br>

    <label>Salary</label><br>
    <input type="text" name="salary">
    <br><br>

    <label>Application Deadline *</label><br>
    <input type="date" name="deadline" min="<?php echo date('Y-m-d'); ?>" required>
    <br><br>

    <button type="submit">Create Job</button>

</form>

<br>

<a href="../dashboard.php">Back to Dashboard</a>

</body>
</html>
