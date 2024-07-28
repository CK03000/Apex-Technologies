<?php include 'header.inc'; // Include the header file ?>

<?php
include 'settings.php'; // Include the settings file containing database credentials

// Create a MySQL connection using credentials from 'settings.php'
$conn = new mysqli($host, $user, $pwd, $sql_db);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error); // Terminate script if connection fails
}

// Fetch job data from the database
$query = "SELECT * FROM jobs"; // SQL query to select all records from the 'jobs' table
$result = $conn->query($query); // Execute the query and store the result set
?>

<section>
  <h1>Open Positions</h1>
  <div id="container">
    <!-- Loop through each row in the result set -->
    <?php while ($row = $result->fetch_assoc()): ?>
    <aside>
      <!-- Display job title, ensuring special characters are escaped -->
      <h2><?= htmlspecialchars($row['title']) ?></h2>
      <!-- Display job reference number, ensuring special characters are escaped -->
      <strong>Position Reference Number:</strong> <?= htmlspecialchars($row['reference_number']) ?><br />
      <!-- Display job description, ensuring special characters are escaped -->
      <strong>Brief Description:</strong> <?= htmlspecialchars($row['description']) ?><br />
      <!-- Display salary range, ensuring special characters are escaped -->
      <strong>Salary Range:</strong> <?= htmlspecialchars($row['salary_range']) ?><br />
      <!-- Display reporting manager, ensuring special characters are escaped -->
      <strong>Reports to:</strong> <?= htmlspecialchars($row['reports_to']) ?><br />
      <!-- Display key responsibilities as a list -->
      <strong>Key Responsibilities:</strong>
      <ul>
        <?php
        // Split the 'key_responsibilities' field into an array by new line
        $responsibilities = explode("\n", $row['key_responsibilities']);
        // Loop through each responsibility and display it as a list item
        foreach ($responsibilities as $responsibility): ?>
          <li><?= htmlspecialchars($responsibility) ?></li>
        <?php endforeach; ?>
      </ul>
      <!-- Display required qualifications, skills, knowledge, and attributes as a list -->
      <strong>Required Qualifications, Skills, Knowledge, and Attributes:</strong>
      <ul>
        <?php
        // Split the 'required_qualifications' field into an array by new line
        $qualifications = explode("\n", $row['required_qualifications']);
        // Loop through each qualification and display it as a list item
        foreach ($qualifications as $qualification): ?>
          <li><?= htmlspecialchars($qualification) ?></li>
        <?php endforeach; ?>
      </ul>
    </aside>
    <?php endwhile; // End of the loop through the result set ?>
  </div>
</section>

<section id="benefits">
  <div id="second">
    <aside>
      <h2 class="ben">Company Benefits</h2>
      <p>Discover the benefits of working with us:</p>
      <ol>
        <!-- List company benefits -->
        <li>Competitive salary packages</li>
        <li>Flexible work hours</li>
        <li>Opportunities for career growth and development</li>
        <li>Healthcare and wellness programs</li>
        <li>Employee discounts and perks</li>
      </ol>
    </aside>
    <aside>
      <h2 class="ben">Workplace Features</h2>
      <p>Workplace features you would enjoy:</p>
      <ol>
        <!-- List workplace features -->
        <li>Area to rest and boost your innovation</li>
        <li>Cafeteria</li>
        <li>Small arcade with multiple games</li>
        <li>A rooftop with the best view of the city</li>
      </ol>
    </aside>
  </div>
</section>

<?php
$conn->close(); // Close the MySQL connection
include 'footer.inc'; // Include the footer file
?>
