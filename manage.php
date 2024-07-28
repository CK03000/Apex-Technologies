<?php
require 'header.inc';
include 'settings.php'; // Include your settings file with DB credentials

// Create a MySQL connection
$conn = new mysqli($host, $user, $pwd, $sql_db);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $eoiNumber = isset($_POST['eoiNumber']) ? $conn->real_escape_string($_POST['eoiNumber']) : '';
    $jobReferenceNumber = isset($_POST['jobReferenceNumber']) ? $conn->real_escape_string($_POST['jobReferenceNumber']) : '';
    $firstName = isset($_POST['firstName']) ? $conn->real_escape_string($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? $conn->real_escape_string($_POST['lastName']) : '';
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : '';

    switch ($action) {
        case 'delete':
            $query = "DELETE FROM eoi WHERE EOInumber = '$eoiNumber'";
            if ($conn->query($query) === TRUE) {
                echo "EOI Number: $eoiNumber has been deleted.";
            } else {
                echo "Error deleting EOI: " . $conn->error;
            }
            break;

        case 'delete_by_job_reference':
            $query = "DELETE FROM eoi WHERE JobReferenceNumber = '$jobReferenceNumber'";
            if ($conn->query($query) === TRUE) {
                echo "All EOIs with Job Reference Number: $jobReferenceNumber have been deleted.";
            } else {
                echo "Error deleting EOIs: " . $conn->error;
            }
            break;

        case 'change_status':
            $query = "UPDATE eoi SET Status = '$status' WHERE EOInumber = '$eoiNumber'";
            if ($conn->query($query) === TRUE) {
                echo "Status for EOI Number: $eoiNumber has been updated to $status.";
            } else {
                echo "Error updating status: " . $conn->error;
            }
            break;
    }
}

// Fetch EOIs
$searchQuery = "SELECT * FROM eoi";
$conditions = [];
if (isset($_GET['jobReferenceNumber']) && $_GET['jobReferenceNumber'] != '') {
    $jobReferenceNumber = $conn->real_escape_string($_GET['jobReferenceNumber']);
    $conditions[] = "JobReferenceNumber = '$jobReferenceNumber'";
}
if (isset($_GET['firstName']) && $_GET['firstName'] != '') {
    $firstName = $conn->real_escape_string($_GET['firstName']);
    $conditions[] = "FirstName LIKE '%$firstName%'";
}
if (isset($_GET['lastName']) && $_GET['lastName'] != '') {
    $lastName = $conn->real_escape_string($_GET['lastName']);
    $conditions[] = "LastName LIKE '%$lastName%'";
}

if (count($conditions) > 0) {
    $searchQuery .= " WHERE " . implode(' AND ', $conditions);
}

// Handle sorting
$sortField = isset($_GET['sortField']) ? $conn->real_escape_string($_GET['sortField']) : 'EOInumber';
$sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'DESC' ? 'DESC' : 'ASC';
$validSortFields = ['EOInumber', 'JobReferenceNumber', 'FirstName', 'LastName', 'Status'];

if (in_array($sortField, $validSortFields)) {
    $searchQuery .= " ORDER BY $sortField $sortOrder";
}

$result = $conn->query($searchQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage EOIs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .main-container {
            width: 90%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
        }

        .custom-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            align-items: center;
            width: 100%;
        }

        .custom-form label {
            text-align: right;
            margin-right: 10px;
        }

        .custom-form input[type="text"], .custom-form select {
            padding: 10px;
            box-sizing: border-box;
            width: 100%;
        }

        .custom-form button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            grid-column: span 2;
        }

        .delete-button {
            background-color: #DC3545;
        }

        .change-status-button {
            background-color: #FFC107;
        }

        .eoi-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .eoi-table, .eoi-table th, .eoi-table td {
            border: 1px solid #ddd;
        }

        .eoi-table th, .eoi-table td {
            padding: 10px;
            text-align: left;
        }

        .action-buttons {
            display: flex;
            align-items: center;
        }

        .action-buttons form {
            display: inline-block;
            margin-right: 5px;
        }

        .table-wrapper {
            max-width: 100%;
            overflow-x: auto;
        }
    </style>
</head>
<body>

<div class="main-container">
    <h2>Manage EOIs</h2>

    <form method="GET" action="manage.php" class="custom-form">
        <label for="jobReferenceNumber">Search by Job Reference Number:</label>
        <input type="text" name="jobReferenceNumber" id="jobReferenceNumber" placeholder="Job Reference Number">
        
        <label for="firstName">Search by First Name:</label>
        <input type="text" name="firstName" id="firstName" placeholder="First Name">
        
        <label for="lastName">Search by Last Name:</label>
        <input type="text" name="lastName" id="lastName" placeholder="Last Name">
        
        <label for="sortField">Sort by:</label>
        <select name="sortField" id="sortField">
            <option value="EOInumber">EOI Number</option>
            <option value="JobReferenceNumber">Job Reference Number</option>
            <option value="FirstName">First Name</option>
            <option value="LastName">Last Name</option>
            <option value="Status">Status</option>
        </select>

        <label for="sortOrder">Order:</label>
        <select name="sortOrder" id="sortOrder">
            <option value="ASC">Ascending</option>
            <option value="DESC">Descending</option>
        </select>

        <button type="submit">Search</button>
    </form>

    <form method="POST" action="manage.php" class="custom-form">
        <label for="deleteJobReferenceNumber">Delete all EOIs by Job Reference Number:</label>
        <input type="text" name="jobReferenceNumber" id="deleteJobReferenceNumber" placeholder="Job Reference Number">
        <button type="submit" name="action" value="delete_by_job_reference" class="delete-button">Delete All</button>
    </form>

    <div class="table-wrapper">
        <?php if ($result->num_rows > 0): ?>
            <table class="eoi-table">
                <thead>
                    <tr>
                        <th>EOI Number</th>
                        <th>Job Reference Number</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['EOInumber']) ?></td>
                            <td><?= htmlspecialchars($row['JobReferenceNumber']) ?></td>
                            <td><?= htmlspecialchars($row['FirstName']) ?></td>
                            <td><?= htmlspecialchars($row['LastName']) ?></td>
                            <td><?= htmlspecialchars($row['Status']) ?></td>
                            <td class="action-buttons">
                                <form method="POST" action="manage.php">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="eoiNumber" value="<?= htmlspecialchars($row['EOInumber']) ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                                <form method="POST" action="manage.php">
                                    <input type="hidden" name="action" value="change_status">
                                    <input type="hidden" name="eoiNumber" value="<?= htmlspecialchars($row['EOInumber']) ?>">
                                    <select name="status" required>
                                        <option value="">Change Status</option>
                                        <option value="New">New</option>
                                        <option value="Current">Current</option>
                                        <option value="Final">Final</option>
                                    </select>
                                    <button type="submit" class="change-status-button">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No EOIs found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
require 'footer.inc';
?>

</body>
</html>
