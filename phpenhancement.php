<body>
  <?php include 'header.inc'; // Include the header file ?>

  <main id="enhance">
    <h1>Website Enhancements</h1>

    <section>
      <h2>Enhancement 1: Store Job Descriptions in a Database and Dynamically Generate HTML with PHP</h2>
      <p>
        This enhancement involves storing job descriptions in a MySQL database and dynamically generating the HTML content using PHP. 
	This ensures that the job listings are always up-to-date and can be easily managed. 
	But it can be easily updated via the Manage Jobs Page as well, just give the reference number and update whatever you want.
      </p>
      <p><strong>Code Snippet:</strong></p>
      <pre><code>
include 'settings.php'; // Include database credentials

$conn = new mysqli($host, $user, $pwd, $sql_db); // Establish database connection

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error); // Handle connection errors
}

$query = "SELECT * FROM jobs"; // SQL query to select all job records
$result = $conn->query($query); // Execute the query

while ($row = $result->fetch_assoc()): ?>
    &lt;h2&gt;&lt;?= htmlspecialchars($row['title']) ?&gt;&lt;/h2&gt;
    &lt;p&gt;&lt;?= htmlspecialchars($row['description']) ?&gt;&lt;/p&gt;
&lt;?php endwhile;

$conn->close(); // Close the database connection
      </code></pre>
      <p>
        See this enhancement in action on our
        <a href="managejobs.php">Jobs Page</a>.
      </p>
      <p>
        <strong>Reference:</strong>
        <a href="https://www.php.net/manual/en/book.mysqli.php">PHP MySQLi Documentation</a>
      </p>
    </section>

    <section>
      <h2>Enhancement 2: Provide Sorting Functionality for EOI Records</h2>
      <p>
        This enhancement allows managers to sort the Expression of Interest (EOI) records based on various fields such as EOI number, job reference number, first name, last name, and status. This feature improves the usability and accessibility of the system.
      </p>
      <p><strong>Code Snippet:</strong></p>
      <pre><code>
$sortField = isset($_GET['sortField']) ? $conn->real_escape_string($_GET['sortField']) : 'EOInumber';
$sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'DESC' ? 'DESC' : 'ASC';
$validSortFields = ['EOInumber', 'JobReferenceNumber', 'FirstName', 'LastName', 'Status'];

if (in_array($sortField, $validSortFields)) {
    $searchQuery .= " ORDER BY $sortField $sortOrder";
}

$result = $conn->query($searchQuery); // Execute the sorted query

while ($row = $result->fetch_assoc()): ?>
    &lt;tr&gt;
        &lt;td&gt;&lt;?= htmlspecialchars($row['EOInumber']) ?&gt;&lt;/td&gt;
        &lt;td&gt;&lt;?= htmlspecialchars($row['JobReferenceNumber']) ?&gt;&lt;/td&gt;
        &lt;td&gt;&lt;?= htmlspecialchars($row['FirstName']) ?&gt;&lt;/td&gt;
        &lt;td&gt;&lt;?= htmlspecialchars($row['LastName']) ?&gt;&lt;/td&gt;
        &lt;td&gt;&lt;?= htmlspecialchars($row['Status']) ?&gt;&lt;/td&gt;
    &lt;/tr&gt;
&lt;?php endwhile;
      </code></pre>
      <p>
        See this enhancement in action on our
        <a href="manage.php">Manage EOIs Page</a>.
      </p>
      <p>
        <strong>Reference:</strong>
        <a href="https://www.php.net/manual/en/book.mysqli.php">PHP MySQLi Documentation</a>
      </p>
    </section>
  </main>

  <?php include 'footer.inc'; // Include the footer file ?>
</body>