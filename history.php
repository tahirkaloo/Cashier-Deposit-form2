<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is a supervisor or admin
$isAdmin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin');
$isSupervisor = isset($_SESSION['role']) && ($_SESSION['role'] === 'supervisor');

$search = "";
$filterUser = "";
$date = "";
$submissions = [];

if (DEMO_MODE) {
    // Generate mock data for Demo Mode
    for ($i = 1; $i <= 5; $i++) {
        $submissions[] = [
            'id' => $i,
            'created_at' => date('Y-m-d H:i:s', strtotime("-$i days")),
            'username' => 'demo_user',
            'name' => 'Demo User',
            'deposit_type' => ($i % 2 == 0) ? 'End of the day' : 'Mid day',
            'drawer_number' => '100' . $i,
            'cash_amount' => number_format($i * 100.50, 2),
            'check21_deposit_amount' => '0.00',
            'check21_deposit_count' => '0',
            'ceo_check_deposit_amount' => '0.00',
            'ceo_check_deposit_count' => '0',
            'manual_check_deposit_amount' => '0.00',
            'manual_check_deposit_count' => '0',
            'money_order_deposit_amount' => '0.00',
            'money_order_deposit_count' => '0',
            'credit_debit_cards_amount' => '50.00',
            'credit_debit_cards_count' => '2',
            'pre_deposit_amount' => '0.00',
            'pre_deposit_count' => '0',
            'total_amount' => number_format(($i * 100.50) + 50, 2),
            'total_count' => '2',
            'verified' => ($i % 2 == 0) ? 1 : 0
        ];
    }
} else {
    // Connect to the database
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    if (!$conn) {
        // Fallback or die - actually if we are not in DEMO_MODE, config says we should be able to connect
        // but if it fails here, we die as per original logic, or handle gracefully.
        die("Connection failed: " . mysqli_connect_error());
    }

    // Build SQL Query
    if ($isAdmin || $isSupervisor) {
        $sql = "SELECT * FROM cashierdeposit";
    } else {
        $sql = "SELECT * FROM cashierdeposit WHERE username = '" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'";
    }

    $whereClauses = [];
    if (!($isAdmin || $isSupervisor)) {
         $whereClauses[] = "username = '" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'";
    }

    // Handle form submission filters
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['search']) && !empty($_POST['search'])) {
            $search = mysqli_real_escape_string($conn, $_POST['search']);
            $whereClauses[] = "(id LIKE '%$search%' OR username LIKE '%$search%' OR name LIKE '%$search%')";
        }
        if (isset($_POST['user']) && !empty($_POST['user'])) {
            $filterUser = mysqli_real_escape_string($conn, $_POST['user']);
            $whereClauses[] = "username = '$filterUser'";
        }
        if (isset($_POST['date']) && !empty($_POST['date'])) {
            $date = mysqli_real_escape_string($conn, $_POST['date']);
            $whereClauses[] = "DATE(created_at) = '$date'";
        }
    }

    if (!empty($whereClauses)) {
        if ($isAdmin || $isSupervisor) {
             $sql .= " WHERE " . implode(" AND ", $whereClauses);
        } else {
             // For regular user, the username restriction is already the base, so we append
             // Wait, I handled regular user base case above differently. Let's unify.
             // Reset SQL for clean build
             $sql = "SELECT * FROM cashierdeposit";
             $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }
    } else {
         if (!$isAdmin && !$isSupervisor) {
              $sql .= " WHERE username = '" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'";
         }
    }

    $sql .= " ORDER BY created_at DESC"; // Good practice to order by date

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $submissions[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History | Deposits Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php showDemoBanner(); ?>

<!-- Navigation -->
<?php include "navbar.php"; ?>

<div class="container-fluid mt-4 mb-5">
    <div class="glass-panel p-4 animate-fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-history me-2 text-primary"></i>Transaction History</h2>
            <?php if ($isAdmin || $isSupervisor): ?>
                <span class="badge bg-info text-dark">Admin View</span>
            <?php endif; ?>
        </div>

        <!-- Filter and search form -->
        <form action="" method="post" class="mb-4">
            <div class="row g-3 align-items-center bg-white p-3 rounded shadow-sm border">
                <?php if ($isAdmin || $isSupervisor): ?>
                <div class="col-md-3">
                    <label class="visually-hidden">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search ID, User, Name" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="visually-hidden">User</label>
                    <select name="user" class="form-select">
                        <option value="">All Users</option>
                        <?php
                        if (!DEMO_MODE) {
                            $userQuery = "SELECT DISTINCT username FROM cashierdeposit";
                            $userResult = mysqli_query($conn, $userQuery);
                            if ($userResult) {
                                while ($userRow = mysqli_fetch_assoc($userResult)) {
                                    $selected = ($filterUser == $userRow['username']) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($userRow['username']) . "\" $selected>" . htmlspecialchars($userRow['username']) . "</option>";
                                }
                            }
                        } else {
                            echo '<option value="demo_user">demo_user</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-3">
                    <label class="visually-hidden">Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date); ?>">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="history.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive table-custom">
            <?php if (!empty($submissions)) : ?>
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Drawer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $row) : ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light text-primary d-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px; font-weight: bold;">
                                            <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($row['username']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($row['deposit_type']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['drawer_number']); ?></span></td>
                                <td class="fw-bold text-success">$<?php echo $row['total_amount']; ?></td>
                                <td>
                                    <?php if ($row['verified']): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Verified</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="viewsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                        <?php if ((!$row['verified'] || $isAdmin || $isSupervisor) && !DEMO_MODE): ?>
                                            <?php if ($row['verified']): ?>
                                                <a href="unverifysubmission.php?id=<?php echo $row['id']; ?>&table=cashierdeposit" class="btn btn-outline-warning" title="Unverify"><i class="fas fa-undo"></i></a>
                                            <?php endif; ?>
                                            <a href="editsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="deletesubmission.php?id=<?php echo $row['id']; ?>&table=cashierdeposit" class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-5">
                    <img src="images/history.png" alt="No Data" style="max-width: 150px; opacity: 0.5;">
                    <p class="text-muted mt-3">No transactions found matching your criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'footer.php'; ?>
</html>

<?php
if (!DEMO_MODE && isset($conn) && $conn) {
    mysqli_close($conn);
}
?>
