
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
$job_id = (int) ($_GET["job_id"] ?? $_POST["job_id"] ?? 0);

if ($job_id <= 0) {
    header("Location: browse_jobs.php");
    exit;
}

// Find the selected job
$stmt = $conn->prepare(
    "SELECT id, title FROM jobs
     WHERE id = ? AND deadline >= CURDATE()"
);

$stmt->bind_param("i", $job_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: browse_jobs.php");
    exit;
}

$job = $result->fetch_assoc();

// Check whether this user has already applied
$check = $conn->prepare(
    "SELECT id FROM applications
     WHERE job_id = ? AND job_seeker_id = ?"
);

$check->bind_param("ii", $job_id, $user_id);
$check->execute();

$alreadyApplied = $check->get_result()->num_rows > 0;

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$alreadyApplied) {

    $cover_letter = trim($_POST["cover_letter"] ?? "");

    if (empty($cover_letter)) {

        $message = "Please write a cover letter.";

    } else {

        $insert = $conn->prepare(
            "INSERT INTO applications
             (job_id, job_seeker_id, cover_letter)
             VALUES (?, ?, ?)"
        );

        $insert->bind_param(
            "iis",
            $job_id,
            $user_id,
            $cover_letter
        );

        if ($insert->execute()) {
            $alreadyApplied = true;
            $message = "Application submitted successfully!";
        } else {
            $message = "Failed to submit application.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Job</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Apply for Job</h1>

<h2><?php echo htmlspecialchars($job["title"]); ?></h2>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if ($alreadyApplied) { ?>

    <p>You have already applied for this job.</p>

<?php } else { ?>

    <form method="POST">

        <input
            type="hidden"
            name="job_id"
            value="<?php echo $job_id; ?>"
        >

        <label>Cover Letter *</label><br>

        <textarea
            name="cover_letter"
            rows="6"
            cols="45"
            placeholder="Explain why you are suitable for this job..."
            required
        ></textarea>

        <br><br>

        <button type="submit">Submit Application</button>

    </form>

<?php } ?>

<br>

<a href="browse_jobs.php">Back to Available Jobs</a>

</body>
</html>
