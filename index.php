<?php
$servername = "localhost";
$username   = "root";
$password   = ""; // no password in XAMPP
$dbname     = "act01";

session_start(); 

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert New Employee
if (isset($_POST["add"])) {
    $firstName = $_POST["first_name"];
    $lastName  = $_POST["last_name"];
    $shiftDate = $_POST["shift_date"];
    $shiftNo   = $_POST['shift_no'];
    $hours     = $_POST['hours'];
    $dutyType  = $_POST['duty_type'];

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

// Update Employee
if (isset($_POST["update"])) {
    $id        = $_POST["id"];
    $firstName = $_POST["first_name"];
    $lastName  = $_POST["last_name"];
    $shiftDate = $_POST["shift_date"];
    $shiftNo   = $_POST['shift_no'];
    $hours     = $_POST['hours'];
    $dutyType  = $_POST['duty_type'];

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

// Delete Employee
if (isset($_POST["delete"])) {
    $id = $_POST["id"];
    $sql = "DELETE FROM employeedetails WHERE DataEntryID='$id'";
    if ($conn->query($sql)) {
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
    $search_no = (int)$_POST["shift_no"];
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Management</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            background: #ece7e8;
            font-family: 'DM Sans', sans-serif;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
        }
        table th {
            background: #ed6c24 !important;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        .title-font {
            font-family: 'Cal Sans', sans-serif; 
        }
        
        .btn-add-style {
            border: 2px solid 	#909878;
            color: #ffffffff;
            background-color: 	#909878;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .btn-add-style:hover {
            box-shadow: inset 0 0 0.55em 0em #ffffffff;
            border-color: #909878;
            color: #ffffffff;
            background-color: #909878;
        }


        .btn-ud-style {
            border: 2px solid 	#a17f7a;
            color: #ffffffff;
            background-color: 	#a17f7a;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .btn-ud-style:hover {
            box-shadow: inset 0 0 0.55em 0em #ffffffff;
            border-color: #a17f7a;
            color: #ffffffff;
            background-color: #a17f7a;
        }

        .btn-delete-style {
            border: 2px solid 	#3d271a;
            color: #ffffffff;
            background-color: 	#3d271a;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .btn-delete-style:hover {
            box-shadow: inset 0 0 0.55em 0em #ffffffff;
            border-color: #3d271a;
            color: #ffffffff;
            background-color: #3d271a;
        }


    </style>
</head>

<!-- BODY -->
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark mb-4" style="background-color: #616651ff;">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fw-bold title-font">EMPLOYEE  MANAGEMENT  SYSTEM</span>
        </div>
    </nav>


    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Add Employee -->
            <div class="col-md-4">
                <div class="card p-3" style="background-color: #fff0e1;">
                    <h5 class="fw-bold">Add Employee</h5>
                    <form method="post">
                        <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="date" name="shift_date" class="form-control mb-2" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="number" name="hours" class="form-control mb-2" placeholder="Hours" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <select name="duty_type" class="form-select mb-2" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                            <option value="OnDuty">On Duty</option>
                            <option value="Late">Late</option>
                            <option value="Overtime">Overtime</option>
                        </select>
                        <button type="submit" name="add" class="btn btn-add-style w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48" class="me-1 align-text-bottom">
                                <g fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="4">
                                <rect width="36" height="36" x="5" y="7" rx="3"/>
                                <path stroke-linecap="round" d="M24 16v16m-8-8h16"/>
                                </g>
                            </svg>
                            Add
                        </button>
                    </form>
                </div>
            </div>

            <!-- Update Employee -->
            <div class="col-md-4">
                <div class="card p-3" style="background-color: #fff0e1;">
                    <h5 class="fw-bold">Update Employee</h5>
                    <form method="post">
                        <input type="number" name="id" class="form-control mb-2" placeholder="Data Entry ID" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="date" name="shift_date" class="form-control mb-2" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <input type="number" name="hours" class="form-control mb-2" placeholder="Hours" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <select name="duty_type" class="form-select mb-2" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                            <option value="OnDuty">On Duty</option>
                            <option value="Late">Late</option>
                            <option value="Overtime">Overtime</option>
                        </select>
                        <button type="submit" name="update"  class="btn btn-ud-style w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="me-2"><path fill="#ffffff" d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1c-2.73 2.71-2.73 7.08 0 9.79s7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29c-3.51 3.48-9.21 3.48-12.72 0c-3.5-3.47-3.53-9.11-.02-12.58s9.14-3.47 12.65 0L21 3v7.12zM12.5 8v4.25l3.5 2.08l-.72 1.21L11 13V8h1.5z"/></svg>
                             Update
                        </button>
                    </form>
                </div>
            </div>

            <!-- Delete Employee -->
            <div class="col-md-4">
                <div class="card p-3" style="background-color: #fff0e1;">
                    <h5 class="fw-bold" >Delete Employee</h5>
                    <form method="post">
                        <input type="number" name="id" class="form-control mb-3" placeholder="Data Entry ID" required style="background: rgba(146, 145, 145, 0.12); border: none;">
                        <button type="submit" name="delete" class="btn btn-delete-style w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="me-2 align-text-bottom">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                <path d="M5.47 6.015v12.514a2.72 2.72 0 0 0 2.721 2.721h7.618a2.72 2.72 0 0 0 2.72-2.72V6.014m-15.235.001h17.412"/>
                                <path d="M8.735 6.015V4.382a1.632 1.632 0 0 1 1.633-1.632h3.264a1.632 1.632 0 0 1 1.633 1.632v1.633M9.824 16.992v-5.439m4.353 5.439v-5.439"/>
                            </g>
                        </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="card p-3 mb-4 w-100">
            <h5 class="fw-bold">Search Employees</h5>
            <form method="post" class="row g-3 align-items-center">
                <div class="d-flex justify-content-center gap-2">
                    <div class="col-md-3">
                        <input type="text" name="last_name" class="form-control" placeholder="Last Name">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="shift_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="shift_no" class="form-control" placeholder="Shift No">
                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-dark btn-custom">🔍 Search</button>
                        <button type="submit" name="export_filtered" class="btn btn-warning btn-custom">Export Filtered</button>
                        <button type="submit" name="export_all" class="btn btn-secondary btn-custom">Export All</button>
                        <button type="submit" name="view_all" class="btn btn-info btn-custom">View All</button>
                    </div>
                </div>
            </form>
        </div>

<?php if ($show_all) { ?>
        <!-- All Employees -->
        <h4 class="fw-bold">All Employees</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
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
                    <?php if ($result_all && $result_all->num_rows > 0) { ?>
                        <?php while ($row = $result_all->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row["DataEntryID"] ?></td>
                            <td><?= $row["FirstName"] ?></td>
                            <td><?= $row["LastName"] ?></td>
                            <td><?= $row["ShiftDate"] ?></td>
                            <td><?= $row["ShiftNo"] ?></td>
                            <td><?= $row["Hours"] ?></td>
                            <td><?= $row["DutyType"] ?></td>
                        </tr>
                        <?php } ?>
                    <?php } 
                    else { ?>
                        <tr><td colspan="7" class="text-center text-muted">No records found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>


        <!-- Filtered Employees -->
        <h4 class="fw-bold">Filtered Employees</h4>
        <div class="table-responsive mb-4">
            <table class="table table-striped table-hover align-middle">
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
                    <?php if ($result_filtered && $result_filtered->num_rows > 0) { ?>
                        <?php while ($row = $result_filtered->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row["DataEntryID"] ?></td>
                            <td><?= $row["FirstName"] ?></td>
                            <td><?= $row["LastName"] ?></td>
                            <td><?= $row["ShiftDate"] ?></td>
                            <td><?= $row["ShiftNo"] ?></td>
                            <td><?= $row["Hours"] ?></td>
                            <td><?= $row["DutyType"] ?></td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="7" class="text-center text-muted">No records match filter</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
<?php } ?>

<!-- Toast Container for Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <?php if (isset($_SESSION['toast'])): ?>
    <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <?= $_SESSION['toast']; ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
    <?php unset($_SESSION['toast']); ?>
  <?php endif; ?>
</div>

<script>
  // Save scroll position before unloading
  window.addEventListener("beforeunload", function () {
    localStorage.setItem("scrollPosition", window.scrollY);
  });

  // Restore scroll position when page loads
  window.addEventListener("load", function () {
    let scrollPosition = localStorage.getItem("scrollPosition");
    if (scrollPosition) {
      window.scrollTo(0, parseInt(scrollPosition));
    }
  });
</script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<!-- END OF BODY -->
</html>
