<?php include 'header.inc'; // Include the header file ?>

<?php
include 'settings.php'; // Include the settings file containing database credentials

// Create a MySQL connection using credentials from 'settings.php'
$conn = new mysqli($host, $user, $pwd, $sql_db);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error); // Terminate script if connection fails
}

// Initialize variables to hold job details
$reference_number = '';
$title = '';
$description = '';
$salary_range = '';
$reports_to = '';
$key_responsibilities = '';
$required_qualifications = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reference_number']) && !isset($_POST['update'])) {
        // Handle job search by reference number
        $reference_number = $conn->real_escape_string($_POST['reference_number']); // Escape the input for security
        
        // Fetch job details from the database
        $query = "SELECT * FROM jobs WHERE reference_number='$reference_number'";
        $result = $conn->query($query);
        
        // Check if the job exists
        if ($result->num_rows > 0) {
            // Fetch the job details into the variables
            $row = $result->fetch_assoc();
            $title = $row['title'];
            $description = $row['description'];
            $salary_range = $row['salary_range'];
            $reports_to = $row['reports_to'];
            $key_responsibilities = $row['key_responsibilities'];
            $required_qualifications = $row['required_qualifications'];
        }
    } elseif (isset($_POST['update'])) {
        // Handle job update
        $reference_number = $conn->real_escape_string($_POST['reference_number']); // Escape the input for security
        $title = $conn->real_escape_string($_POST['title']); // Escape the input for security
        $description = $conn->real_escape_string($_POST['description']); // Escape the input for security
        $salary_range = $conn->real_escape_string($_POST['salary_range']); // Escape the input for security
        $reports_to = $conn->real_escape_string($_POST['reports_to']); // Escape the input for security
        $key_responsibilities = $conn->real_escape_string($_POST['key_responsibilities']); // Escape the input for security
        $required_qualifications = $conn->real_escape_string($_POST['required_qualifications']); // Escape the input for security
        
        // Update job details in the database
        $query = "UPDATE jobs SET 
            title='$title',
            description='$description',
            salary_range='$salary_range',
            reports_to='$reports_to',
            key_responsibilities='$key_responsibilities',
            required_qualifications='$required_qualifications'
            WHERE reference_number='$reference_number'";
        
        // Check if the update was successful
        if ($conn->query($query) === TRUE) {
            echo "Job details updated successfully."; // Display success message
        } else {
            echo "Error updating job details: " . $conn->error; // Display error message
        }
    }
}
?>

<section>
  <h1>Manage Job Descriptions</h1>
  <!-- Form for searching job details by reference number -->
  <form method="post" action="managejobs.php">
    <label for="reference_number">Job Reference Number:</label>
    <input type="text" id="reference_number" name="reference_number" value="<?= htmlspecialchars($reference_number) ?>" required>
    <button type="submit">Search</button>
  </form>

  <!-- Display job details form if job title is available -->
  <?php if ($title): ?>
  <form method="post" action="managejobs.php">
    <input type="hidden" name="reference_number" value="<?= htmlspecialchars($reference_number) ?>">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
    
    <label for="description">Description:</label>
    <textarea id="description" name="description" required><?= htmlspecialchars($description) ?></textarea>
    
    <label for="salary_range">Salary Range:</label>
    <input type="text" id="salary_range" name="salary_range" value="<?= htmlspecialchars($salary_range) ?>" required>
    
    <label for="reports_to">Reports To:</label>
    <input type="text" id="reports_to" name="reports_to" value="<?= htmlspecialchars($reports_to) ?>" required>
    
    <label for="key_responsibilities">Key Responsibilities:</label>
    <textarea id="key_responsibilities" name="key_responsibilities" required><?= htmlspecialchars($key_responsibilities) ?></textarea>
    
    <label for="required_qualifications">Required Qualifications:</label>
    <textarea id="required_qualifications" name="required_qualifications" required><?= htmlspecialchars($required_qualifications) ?></textarea>
    
    <button type="submit" name="update">Update</button>
  </form>
  <?php endif; ?>
</section>

<?php
$conn->close(); // Close the MySQL connection
include 'footer.inc'; // Include the footer file
?>
