<?php
include 'header.inc'; // Include the header file
include 'settings.php'; // Include the settings file containing database credentials

// Prevent direct access to this script
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: apply.php'); // Redirect to 'apply.php' if accessed directly
    exit; // Exit the script
}

// Create a MySQL connection using credentials from 'settings.php'
$conn = new mysqli($host, $user, $pwd, $sql_db);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error); // Terminate script if connection fails
}

// Check if the 'eoi' table exists and create it if not
$tableCheckQuery = "SHOW TABLES LIKE 'eoi'"; // SQL query to check if table exists
$result = $conn->query($tableCheckQuery);

if ($result->num_rows == 0) {
    // Table does not exist, create it
    $createTableQuery = "
    CREATE TABLE eoi (
        EOInumber INT(11) AUTO_INCREMENT PRIMARY KEY, // Auto-incrementing primary key
        JobReferenceNumber VARCHAR(5) NOT NULL, // Job reference number
        FirstName VARCHAR(50), // First name
        LastName VARCHAR(50), // Last name
        StreetAddress VARCHAR(100), // Street address
        SuburbTown VARCHAR(50), // Suburb or town
        State ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL, // State
        Postcode CHAR(4) NOT NULL, // Postcode
        EmailAddress VARCHAR(100), // Email address
        PhoneNumber VARCHAR(20), // Phone number
        Skill1 VARCHAR(50), // Skill 1
        Skill2 VARCHAR(50), // Skill 2
        Skill3 VARCHAR(50), // Skill 3
        Skill4 VARCHAR(50), // Skill 4
        Skill5 VARCHAR(50), // Skill 5
        OtherSkills TEXT, // Other skills
        Status ENUM('New','Current','Final') DEFAULT 'New', // Status
        DOB DATE, // Date of birth
        Gender ENUM('Male','Female','Other') // Gender
    )";

    if ($conn->query($createTableQuery) === FALSE) {
        die('Error creating table: ' . $conn->error); // Terminate script if table creation fails
    }
}

// Initialize an array to hold error messages
$errors = [];

// Sanitize and validate inputs
$jobReferenceNumber = isset($_POST['JobReferenceNumber']) ? trim($_POST['JobReferenceNumber']) : '';
$firstName = isset($_POST['FirstName']) ? trim($_POST['FirstName']) : '';
$lastName = isset($_POST['LastName']) ? trim($_POST['LastName']) : '';
$streetAddress = isset($_POST['StreetAddress']) ? trim($_POST['StreetAddress']) : '';
$suburbTown = isset($_POST['SuburbTown']) ? trim($_POST['SuburbTown']) : '';
$state = isset($_POST['State']) ? trim($_POST['State']) : '';
$postcode = isset($_POST['PostCode']) ? trim($_POST['PostCode']) : ''; 
$emailAddress = isset($_POST['EmailAddress']) ? trim($_POST['EmailAddress']) : '';
$phoneNumber = isset($_POST['PhoneNumber']) ? trim($_POST['PhoneNumber']) : '';
$gender = isset($_POST['Gender']) ? trim($_POST['Gender']) : '';
$skills = isset($_POST['Skills']) ? $_POST['Skills'] : [];
$otherSkills = isset($_POST['OtherSkills']) ? trim($_POST['OtherSkills']) : '';

// Attempt to create a DateTime object from the DOB input
$dob = isset($_POST['DOB']) ? $_POST['DOB'] : '';
try {
    $birthdate = new DateTime($dob); // Try to create DateTime object
    $dob = $birthdate->format('Y-m-d'); // Format date for SQL
} catch (Exception $e) {
    $errors[] = "Invalid date format."; // Add error message if date format is invalid
}

// Validate inputs
if (!preg_match('/^[A-Za-z0-9]{5}$/', $jobReferenceNumber)) {
    $errors[] = "Invalid Job Reference Number."; // Validate job reference number
}
if (!preg_match('/^[a-zA-Z]{1,50}$/', $firstName)) {
    $errors[] = "Invalid First Name."; // Validate first name
}
if (!preg_match('/^[a-zA-Z]{1,50}$/', $lastName)) {
    $errors[] = "Invalid Last Name."; // Validate last name
}
if (!in_array($state, ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'])) {
    $errors[] = "Invalid State."; // Validate state
}
if (!preg_match('/^[0-9]{4}$/', $postcode)) {
    $errors[] = "Invalid Postcode."; // Validate postcode
} else {
    // Additional postcode range validation
    $postcode = (int)$postcode;
    $validPostcode = false;
    switch ($state) {
        case 'NSW':
            if (($postcode >= 2000 && $postcode <= 2599) || ($postcode >= 2619 && $postcode <= 2898) || ($postcode >= 2921 && $postcode <= 2999)) {
                $validPostcode = true;
            }
            break;
        case 'ACT':
            if (($postcode >= 2600 && $postcode <= 2618) || ($postcode >= 2900 && $postcode <= 2920)) {
                $validPostcode = true;
            }
            break;
        case 'VIC':
            if ($postcode >= 3000 && $postcode <= 3999) {
                $validPostcode = true;
            }
            break;
        case 'QLD':
            if ($postcode >= 4000 && $postcode <= 4999) {
                $validPostcode = true;
            }
            break;
        case 'SA':
            if ($postcode >= 5000 && $postcode <= 5799) {
                $validPostcode = true;
            }
            break;
        case 'WA':
            if ($postcode >= 6000 && $postcode <= 6797) {
                $validPostcode = true;
            }
            break;
        case 'TAS':
            if ($postcode >= 7000 && $postcode <= 7799) {
                $validPostcode = true;
            }
            break;
        case 'NT':
            if ($postcode >= 800 && $postcode <= 899) { // Note NT postcodes are 0800–0899, leading zeros are omitted
                $validPostcode = true;
            }
            break;
        default:
            $validPostcode = false;
            break;
    }
    if (!$validPostcode) {
        $errors[] = "Postcode does not match the State."; // Add error if postcode does not match the state
    }
}

if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid Email Address."; // Validate email address
}
if (!preg_match('/^[0-9 ]{8,12}$/', $phoneNumber)) {
    $errors[] = "Invalid Phone Number."; // Validate phone number
}
if (!in_array($gender, ['Male', 'Female', 'Other'])) {
    $errors[] = "Invalid Gender."; // Validate gender
}

// Check if "Other Skills" is required
if (in_array('OtherSkills', $skills) && empty($otherSkills)) {
    $errors[] = "Other Skills field cannot be empty if 'Other Skills' is selected."; // Add error if Other Skills is selected but not provided
}

// Assuming $birthdate is successfully created from the above sanitization code
$today = new DateTime(); // Get today's date
$diff = $today->diff($birthdate); // Calculate age
$age = $diff->y; // Extract age in years
if ($age < 15 || $age > 80) {
    $errors[] = "Applicant must be between 15 and 80 years old."; // Add error if age is not between 15 and 80
}

// Check if any errors were found
if (!empty($errors)) {
    echo "There were errors in your form submission:<br>"; // Display errors
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
    exit; // Exit script if there are errors
}

// Prepare an insert statement
$sql = "INSERT INTO eoi (
    JobReferenceNumber, 
    FirstName, 
    LastName, 
    DOB, 
    Gender, 
    StreetAddress, 
    SuburbTown, 
    State, 
    Postcode, 
    EmailAddress, 
    PhoneNumber, 
    Skill1, 
    Skill2, 
    Skill3, 
    Skill4, 
    Skill5, 
    OtherSkills, 
    Status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo "Error preparing statement: " . $conn->error; // Display error if statement preparation fails
    $conn->close();
    exit; // Exit script
}

// Convert skills array into individual skill variables
$skill1 = isset($skills[0]) ? $skills[0] : null;
$skill2 = isset($skills[1]) ? $skills[1] : null;
$skill3 = isset($skills[2]) ? $skills[2] : null;
$skill4 = isset($skills[3]) ? $skills[3] : null;
$skill5 = isset($skills[4]) ? $skills[4] : null;
$status = "New"; // Default status for new entries

// Bind parameters
$stmt->bind_param("ssssssssssssssssss", 
    $jobReferenceNumber, 
    $firstName, 
    $lastName, 
    $dob, 
    $gender, 
    $streetAddress, 
    $suburbTown, 
    $state, 
    $postcode, 
    $emailAddress, 
    $phoneNumber, 
    $skill1, 
    $skill2, 
    $skill3, 
    $skill4, 
    $skill5, 
    $otherSkills, 
    $status
);

// Execute the query and check for success
if ($stmt->execute()) {
    $eoiNumber = $stmt->insert_id; // Get the auto-generated EOI number
    echo "<div style='text-align: center; margin-top: 50px;'>";
    echo '<section class="contact-us">
    <h2>You are one step closer to being an Apex</h2>
    <p>
      Your application has been recieved, If you have any questions, please feel free to reach out to us at
      <a href="mailto:apextechnology03@gmail.com">apextechnology03@gmail.com</a>.
    </p>
  </section>';
      echo "<h1 style='font-size: 24px; color: purple;'>Record added successfully.</h1>";
    echo "<h2>Your EOI Number is: <span style='color: darkpurple;'>$eoiNumber</span></h2>"; // Display EOI number
    echo "</div>";
} else {
    echo "<p style='color: red; text-align: center;'>Error: " . $stmt->error . "</p>"; // Display error if query execution fails
}

$stmt->close(); // Close the statement
$conn->close(); // Close the connection
include 'footer.inc'; // Include the footer file
?>
