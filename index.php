<?php
// ---------------------------
// Database connection config
// ---------------------------
$servername = "localhost";
$username = "root";
$password = ""; // no password in XAMPP
$dbname = "employeedetails";

session_start();

// ---------------------------
// Connect to MySQL database
// ---------------------------
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------------------
// Insert New Employee handler
// ---------------------------
if (isset($_POST["add"])) {
  $firstName = $_POST["first_name"];
  $lastName = $_POST["last_name"];
  $shiftDate = $_POST["shift_date"];
  $shiftNo = (int)$_POST['shift_no'];
  $hours = (int)$_POST['hours'];
  $dutyType = $_POST['duty_type'];

  // Data validation: no negative hours or shift number
  if ($hours < 0 || $shiftNo < 0) {
    $_SESSION['toast'] = "❌ Hours and Shift No must not be negative!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }

  $sql = "INSERT INTO employeedetails (FirstName, LastName, ShiftDate, ShiftNo, Hours, DutyType)
      VALUES ('$firstName', '$lastName', '$shiftDate', '$shiftNo', '$hours', '$dutyType')";

  if ($conn->query($sql)) {
    $_SESSION['toast'] = "Employee added successfully!";
  } else {
    $_SESSION['toast'] = "❌ Error adding employee!";
  }
  header("Location: " . $_SERVER['PHP_SELF']); // refresh to show toast
  exit;
}

// ---------------------------
// Update Employee handler
// ---------------------------
if (isset($_POST["update"])) {
  $id = $_POST["id"];
  $firstName = $_POST["first_name"];
  $lastName = $_POST["last_name"];
  $shiftDate = $_POST["shift_date"];
  $shiftNo = (int)$_POST['shift_no'];
  $hours = (int)$_POST['hours'];
  $dutyType = $_POST['duty_type'];

  // Data validation: no negative hours or shift number
  if ($hours < 0 || $shiftNo < 0) {
    $_SESSION['toast'] = "❌ Hours and Shift No must not be negative!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }

  $sql = "UPDATE employeedetails
      SET FirstName='$firstName', LastName='$lastName', ShiftDate='$shiftDate',
        ShiftNo='$shiftNo', Hours='$hours', DutyType='$dutyType'
      WHERE DataEntryID='$id'";
  if ($conn->query($sql)) {
    $_SESSION['toast'] = "Employee updated successfully!";
  } else {
    $_SESSION['toast'] = "❌ Error updating employee!";
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// ---------------------------
// Delete Employee handler
// ---------------------------
if (isset($_POST["delete"])) {
    $id = $_POST["id"];
    $sql = "DELETE FROM employeedetails WHERE DataEntryID='$id'";
    if ($conn->query($sql)) {
        // Check if table is now empty, then reset AUTO_INCREMENT
        $check = $conn->query("SELECT COUNT(*) as cnt FROM employeedetails");
        $row = $check ? $check->fetch_assoc() : null;
        if ($row && $row['cnt'] == 0) {
            $conn->query("ALTER TABLE employeedetails AUTO_INCREMENT = 1");
        }
        $_SESSION['toast'] = "Employee deleted successfully!";
    } else {
        $_SESSION['toast'] = "❌ Error deleting employee!";
    }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}



// Search Query
$where = [];

if (!empty($_POST["last_name"])) {
    $search_name = $conn->real_escape_string($_POST["last_name"]);
    $where[] = "(FirstName LIKE '%$search_name%' OR LastName LIKE '%$search_name%')";
}

if (!empty($_POST["shift_date"])) {
    $search_date = $conn->real_escape_string($_POST["shift_date"]);
    $where[] = "ShiftDate = '$search_date'";
}

if (!empty($_POST["shift_no"])) {
    $search_no = (int) $_POST["shift_no"];
    $where[] = "ShiftNo = $search_no";
}

// Base queries
$sql_all = "SELECT * FROM employeedetails";
$sql_filtered = $sql_all;

if (!empty($where)) {
    $sql_filtered .= " WHERE " . implode(" AND ", $where);
}

$result_all = null; // default hidden
if (isset($_POST["view_all"])) {
    $result_all = $conn->query($sql_all);
}

$result_filtered = $conn->query($sql_filtered);

// when show all button is clicked
$show_all = false; // default is filtered employees

if (isset($_POST["view_all"])) {
    $show_all = true;
}

// Run queries conditionally
if ($show_all) {
    $result_all = $conn->query($sql_all);
    $result_filtered = null; // disable filtered
} else {
    $result_filtered = $conn->query($sql_filtered);
    $result_all = null; // disable all
}

// Export CSV (Filtered or All)
if (isset($_POST["export_all"]) || isset($_POST["export_filtered"])) {
    $export_sql = isset($_POST["export_filtered"]) ? $sql_filtered : $sql_all;
    $exportResult = $conn->query($export_sql);

    if ($exportResult->num_rows > 0) {
        $filename = isset($_POST["export_filtered"]) ? "employees_filtered.csv" : "employees_all.csv";
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment;filename=$filename");

        $output = fopen("php://output", "w");
        fputcsv($output, ["DataEntryID", "FirstName", "LastName", "ShiftDate", "ShiftNo", "Hours", "DutyType"]);

        while ($row = $exportResult->fetch_assoc()) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    } else {
        echo "<script>alert('No data to export');</script>";
    }
}

if (isset($_POST['clear_filter'])) {
    unset($_POST['last_name'], $_POST['shift_date'], $_POST['shift_no']);
    $show_all = true;
    // Force reload with no filters
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Employee Management</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <!-- Google Fonts: Poppins (Material recommended) -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <!-- Custom styles moved to style.css -->
</head>

<body>

    <!-- SPLASH SCREEN -->
    <!--
      Splash screen shown on page load. Includes:
      - Company logo (office-building.png) for branding
      - Bootstrap spinner for loading animation
      - System title for context
      The splash screen is hidden via JavaScript after the page loads.
    -->
    <div id="splash-screen" class="d-flex justify-content-center align-items-center flex-column">
          <!-- Bootstrap spinner for loading effect -->
        <div class="spinner-border text-light mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
        <!-- Company logo for branding -->
        <img src="office-building.png" alt="Company Logo" style="height:56px;width:auto;margin-bottom:18px;filter: brightness(0) invert(1);">
        <!-- System title -->
        <h2 class="text-light fw-bold">Employee Management System</h2>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary); padding: 15px 30px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span class="navbar-brand mb-0 h1 fw-bold" style="color: var(--text-light);">
              <img src="office-building.png" alt="Company Logo" style="height:32px;vertical-align:middle;margin-right:10px;filter: brightness(0) invert(1);">
              EMPLOYEE MANAGEMENT SYSTEM
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addModal">
                        <span class="material-icons align-middle me-1">person_add</span> Add
                    </button>
                    <button type="button" class="btn btn-outline-warning d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#updateModal">
                        <span class="material-icons align-middle me-1">edit</span> Update
                    </button>
                    <button type="button" class="btn btn-outline-danger d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <span class="material-icons align-middle me-1">delete</span> Delete
                    </button>
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <span class="material-icons align-middle me-1">search</span> Search
                    </button>
                    <button id="toggleDarkMode" class="icon-darkmode-btn ms-2" title="Toggle dark/light mode" style="background:none;border:none;outline:none;padding:0;display:flex;align-items:center;">
                      <span id="darkModeIconSwitch" class="material-icons" aria-hidden="true">dark_mode</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <!-- Google Material Icons CDN -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <!-- Main Content: Only Employees Table -->
        <div id="content" class="flex-grow-1 p-4">
            <div id="all">
                <?php if (!empty($_POST['last_name']) || !empty($_POST['shift_date']) || !empty($_POST['shift_no'])): ?>
                <div class="search-query-card mb-2" id="search-query-card">
                    <?php if (!empty($_POST['last_name'])): ?>
                        <span class="search-query-chip">Name: <?= htmlspecialchars($_POST['last_name']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($_POST['shift_date'])): ?>
                        <span class="search-query-chip">Date: <?= htmlspecialchars($_POST['shift_date']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($_POST['shift_no'])): ?>
                        <span class="search-query-chip">Shift: <?= htmlspecialchars($_POST['shift_no']) ?></span>
                    <?php endif; ?>
                    <form method="post" class="d-inline">
                        <button type="submit" name="clear_filter" class="query-card-btn ms-2">Clear Filter</button>
                    </form>
                    <button type="button" class="query-card-btn ms-2" data-bs-toggle="modal" data-bs-target="#searchModal">+ Add Filter</button>
                    <form method="post" class="d-inline ms-2" id="exportFilteredForm">
                        <input type="hidden" name="last_name" value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
                        <input type="hidden" name="shift_date" value="<?= isset($_POST['shift_date']) ? htmlspecialchars($_POST['shift_date']) : '' ?>">
                        <input type="hidden" name="shift_no" value="<?= isset($_POST['shift_no']) ? htmlspecialchars($_POST['shift_no']) : '' ?>">
                        <button type="submit" name="export_filtered" class="query-card-btn">Export Filtered</button>
                    </form>
                </div>
                <?php endif; ?>
                <div class="row mb-3 align-items-center" style="max-width:1100px;margin-left:auto;margin-right:auto;">
          <div class="col-md-6 d-flex flex-wrap gap-2 align-items-center">
            <h4 class="fw-bold mb-0" style="display:inline;font-family:'Segoe UI', 'Liberation Sans', 'DejaVu Sans', 'Arial', 'sans-serif';"> 
              <?= $show_all ? 'All Employees' : 'Filtered Employees' ?>
            </h4>
          </div>
          <div class="col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0 align-items-center gap-2">
            <?php if ($show_all) { ?>
              <form method="post" class="d-inline ms-2" id="exportAllForm">
                <button type="submit" name="export_all" class="btn btn-dark">Export All</button>
              </form>
            <?php } ?>
            <?php if (!$show_all) { ?>
              <form method="post" class="d-inline">
                <button type="submit" name="view_all" class="btn btn-success">View All</button>
              </form>
            <?php } ?>
          </div>
                </div>
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First</th>
                            <th>Last</th>
                            <th>Shift Date</th>
                            <th>Shift No</th>
                            <th>Hours</th>
                            <th>Duty Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = $show_all ? $result_all : $result_filtered;
                        if ($data && $data->num_rows > 0) {
                            while ($row = $data->fetch_assoc()) {
                                echo "<tr>
                    <td>{$row['DataEntryID']}</td>
                    <td>{$row['FirstName']}</td>
                    <td>{$row['LastName']}</td>
                    <td>{$row['ShiftDate']}</td>
                    <td>{$row['ShiftNo']}</td>
                    <td>{$row['Hours']}</td>
                    <td>{$row['DutyType']}</td>
                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade custom-modal" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 370px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addModalLabel">Add Employee</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post" id="addForm">
              <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required>
              <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required>
              <input type="date" name="shift_date" class="form-control mb-2" required>
              <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No" required>
              <input type="number" name="hours" class="form-control mb-2" placeholder="Hours" required>
              <select name="duty_type" class="form-select mb-2" required>
                <option value="OnDuty">On Duty</option>
                <option value="Late">Late</option>
                <option value="Overtime">Overtime</option>
              </select>
              <button type="submit" name="add" class="btn btn-primary w-100">Add</button>
            </form>
            <div class="text-center mt-3">
              <a href="#" id="updateInsteadLink" class="forgot-link">Update instead?</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade custom-modal" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 370px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="updateModalLabel">Update Employee</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post">
              <input type="number" name="id" class="form-control mb-2" placeholder="Data Entry ID" required>
              <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required>
              <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required>
              <input type="date" name="shift_date" class="form-control mb-2" required>
              <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No" required>
              <input type="number" name="hours" class="form-control mb-2" placeholder="Hours" required>
              <select name="duty_type" class="form-select mb-2" required>
                <option value="OnDuty">On Duty</option>
                <option value="Late">Late</option>
                <option value="Overtime">Overtime</option>
              </select>
              <button type="submit" name="update" class="btn btn-warning w-100">Update</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade custom-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 370px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Delete Employee</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post">
              <input type="number" name="id" class="form-control mb-3" placeholder="Data Entry ID" required>
              <button type="submit" name="delete" class="btn btn-danger w-100">Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Modal -->
    <div class="modal fade custom-modal" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 370px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="searchModalLabel">Search Employees</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post" class="row g-3" id="employee-search-form">
              <div class="col-md-12 mb-2">
                <input type="text" name="last_name" class="form-control" placeholder="Name" value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
              </div>
              <div class="col-md-12 mb-2">
                <input type="date" name="shift_date" class="form-control" value="<?= isset($_POST['shift_date']) ? htmlspecialchars($_POST['shift_date']) : '' ?>">
              </div>
              <div class="col-md-12 mb-2">
                <input type="number" name="shift_no" class="form-control" placeholder="Shift No" value="<?= isset($_POST['shift_no']) ? htmlspecialchars($_POST['shift_no']) : '' ?>">
              </div>
              <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary" id="search-btn">Search</button>
                <button type="submit" name="export_filtered" class="btn btn-secondary">Export Filtered</button>
                <button type="submit" name="export_all" class="btn btn-dark">Export All</button>
                <button type="submit" name="view_all" class="btn btn-success">View All</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <?php if (isset($_SESSION['toast'])): ?>
            <div class="toast align-items-center text-bg-success border-0 show">
                <div class="d-flex">
                    <div class="toast-body"><?= $_SESSION['toast']; ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
            <?php unset($_SESSION['toast']); ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

    <footer class="text-center py-3" style="background: var(--surface); color: var(--primary-dark); font-size: 1.05rem; border-top: 1px solid var(--primary-light); margin-top: 32px;">
      Powered by <strong>Angelica Gregorio</strong> and <strong>Ysabella Santos</strong>
    </footer>
</body>

</html>