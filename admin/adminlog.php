<?php
// Include database connection parameters
require_once '../db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to retrieve logs from the database
function getLogs($conn, $filterByAction = '', $sortOption = 'date_desc') {
    $logs = [];
    
    // Prepare SQL query based on filter and sorting options
    $sql = "SELECT * FROM logs";
    if (!empty($filterByAction)) {
        $sql .= " WHERE action LIKE ?";
    }
    if ($sortOption === 'date_asc') {
        $sql .= " ORDER BY created_at ASC"; // Using 'created_at' column for ascending order
    } else {
        $sql .= " ORDER BY created_at DESC"; // Using 'created_at' column for descending order
    }
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    if (!empty($filterByAction)) {
        $filterByAction = "%$filterByAction%";
        $stmt->bind_param("s", $filterByAction);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch logs
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row['created_at'] . ' - User ID: ' . $row['user_id'] . ' - Name: ' . $row['name'] . ' - Action: ' . $row['action'];
    }
    
    return $logs;
}


// Database connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    exit; // Exit if connection fails
}

// Sorting options
$sortingOptions = [
    'date_desc' => 'Date (Newest First)',
    'date_asc' => 'Date (Oldest First)',
];

// Default sorting option
$defaultSorting = 'date_desc';

// Filter by action
$filterByAction = isset($_GET['action']) ? $_GET['action'] : '';

// Apply sorting
$sortOption = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sortingOptions) ? $_GET['sort'] : $defaultSorting;

// Retrieve logs from the database
$logEntries = getLogs($conn, $filterByAction, $sortOption);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logs</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <?php include '../admin/admin-navbar.php'; ?>
    <div class="container">
        <h1 class="my-4">Admin Logs</h1>

        <!-- Sorting dropdown -->
        <div class="mb-3">
            <label for="sort">Sort by:</label>
            <select id="sort" class="form-select" onchange="location = this.value;">
                <?php foreach ($sortingOptions as $value => $label) : ?>
                    <option value="?sort=<?php echo $value; ?>" <?php echo $sortOption === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filter by action -->
        <div class="mb-3">
            <form action="" method="get">
                <label for="action">Search:</label>
                <input type="text" id="action" name="action" class="form-control" value="<?php echo htmlspecialchars($filterByAction); ?>">
                <button type="submit" class="btn btn-primary mt-2">Search</button>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (!empty($logEntries)) : ?>
                    <ul class="list-group">
                        <?php foreach ($logEntries as $entry) : ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($entry); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>No logs found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
