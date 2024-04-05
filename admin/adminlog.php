<?php
// Include database connection parameters
require_once '../db_connect.php';

// Function to retrieve logs from the database
function getLogs($conn, $filterByAction = '', $sortOption = 'date_desc', $page = 1, $perPage = 10) {
    $logs = [];
    
    // Calculate offset based on page number and number of logs per page
    $offset = ($page - 1) * $perPage;
    
    // Prepare SQL query based on filter, sorting, and pagination options
    $sql = "SELECT * FROM logs";
    if (!empty($filterByAction)) {
        $sql .= " WHERE action LIKE ?";
    }
    if ($sortOption === 'date_asc') {
        $sql .= " ORDER BY created_at ASC"; // Using 'created_at' column for ascending order
    } else {
        $sql .= " ORDER BY created_at DESC"; // Using 'created_at' column for descending order
    }
    $sql .= " LIMIT ?, ?";
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    if (!empty($filterByAction)) {
        $filterByAction = "%$filterByAction%";
        $stmt->bind_param("sii", $filterByAction, $offset, $perPage);
    } else {
        $stmt->bind_param("ii", $offset, $perPage);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch logs
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row; // Store each row as an associative array
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

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20; // Number of logs per page

// Retrieve logs from the database
$logEntries = getLogs($conn, $filterByAction, $sortOption, $page, $perPage);
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

        <div class="container my-3 animate__animated animate__fadeIn animate__faster shadow rounded">
            <div class="table-responsive bg-light rounded shadow">
                <?php if (!empty($logEntries)) : ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logEntries as $entry) : ?>
                                <tr>
                                    <td><?php echo date('Y-m-d h:i:s A', strtotime($entry['created_at'])); ?></td>
                                    <td><?php echo ($entry['user_id']); ?></td>
                                    <td><?php echo ($entry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['action']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page >= 1) : ?>
                                <li class="page-number"
                                    aria-current="page"
                                    class="page-item active">
                                    <span class="page-link">
                                        <?php "Page number: " . $page; echo $page; ?>
                                    </span>
                                </li>
                            <?php endif; ?>
                            <?php if ($page > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($filterByAction) ? '&action=' . htmlspecialchars($filterByAction) : ''; ?><?php echo !empty($sortOption) ? '&sort=' . htmlspecialchars($sortOption) : ''; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo; Previous</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (count($logEntries) === $perPage) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($filterByAction) ? '&action=' . htmlspecialchars($filterByAction) : ''; ?><?php echo !empty($sortOption) ? '&sort=' . htmlspecialchars($sortOption) : ''; ?>" aria-label="Next">
                                        <span aria-hidden="true"> Next &raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php else : ?>
                    <p>No logs found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
