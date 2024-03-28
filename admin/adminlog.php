<?php
// Read the contents of the action log file
$actionLog = file_get_contents('../actionlog.txt');

// Explode the contents into an array of log entries
$logEntries = explode("\n", $actionLog);

// Sorting options
$sortingOptions = [
    'date_desc' => 'Date (Newest First)',
    'date_asc' => 'Date (Oldest First)',
];

// Default sorting option
$defaultSorting = 'date_desc';

// Filter by action
$filterByAction = isset($_GET['action']) ? $_GET['action'] : '';

// Apply filter
if (!empty($filterByAction)) {
    $filteredEntries = [];
    foreach ($logEntries as $entry) {
        if (strpos($entry, $filterByAction) !== false) {
            $filteredEntries[] = $entry;
        }
    }
    $logEntries = $filteredEntries;
}

// Apply sorting
if (isset($_GET['sort']) && array_key_exists($_GET['sort'], $sortingOptions)) {
    $sortOption = $_GET['sort'];
    if ($sortOption === 'date_desc') {
        sort($logEntries);
    } elseif ($sortOption === 'date_asc') {
        rsort($logEntries);
    }
}
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
