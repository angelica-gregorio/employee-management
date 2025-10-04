<?php
$servername = "localhost";
$username   = "root";
$password   = ""; // no password in XAMPP
$dbname     = "employeedetails";

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

    <!------------------------------------------------- HTML PART ---------------------------------------------->

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
    <link href="style.css" rel="stylesheet">
</head>

<!-- BODY -->
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark mb-4" style="background-color: #204f2b; padding: 15px 30px;" >
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fw-bold title-font">EMPLOYEE  MANAGEMENT  SYSTEM</span>
        </div>
    </nav>

    <!-- Employee Tabs -->
    <div class="card p-3 mb-4" style="background-color: #eccedaff; max-width: 900px; ">
        <ul class="nav nav-tabs" id="employeeTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" style="color: #204f2b; font-family: 'Cal Sans', sans-serif;" id="add-tab" data-bs-toggle="tab" data-bs-target="#add" type="button" role="tab">ADD EMPLOYEE</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" style="color: #204f2b; font-family: 'Cal Sans', sans-serif;" id="update-tab" data-bs-toggle="tab" data-bs-target="#update" type="button" role="tab">UPDATE EMPLOYEE</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" style="color: #204f2b; font-family: 'Cal Sans', sans-serif;" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab">DELETE EMPLOYEE</button>
            </li>
        </ul>

        <div class="tab-content pt-3">
            <!-- Add Form -->
            <div class="tab-pane fade" id="add" role="tabpanel">
                <form method="post">
                    <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required >
                    <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required >
                    <input type="date" name="shift_date" class="form-control mb-2" required>
                    <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No" required >
                    <input type="number" name="hours" class="form-control mb-2" placeholder="Hours" required >
                    <select name="duty_type" class="form-select form-control mb-2" required >
                        <option value="OnDuty">On Duty</option>
                        <option value="Late">Late</option>
                        <option value="Overtime">Overtime</option>
                    </select>
                    <button type="submit" name="add" class="btn btn-style w-100">Add</button>
                </form>
            </div>

            <!-- Update Form -->
            <div class="tab-pane fade" id="update" role="tabpanel">
                <form method="post">
                    <input type="number" name="id" class="form-control mb-2" placeholder="Data Entry ID" required>
                    <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required >
                    <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required >
                    <input type="date" name="shift_date" class="form-control mb-2" required >
                    <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No" required >
                    <input type="number" name="hours" class="form-control mb-2" placeholder="Hours" required >
                    <select name="duty_type" class="form-select form-control mb-2" required >
                        <option value="OnDuty">On Duty</option>
                        <option value="Late">Late</option>
                        <option value="Overtime">Overtime</option>
                    </select>
                    <button type="submit" name="update" class="btn btn-style w-100">Update</button>
                </form>
            </div>

            <!-- Delete Form -->
            <div class="tab-pane fade" id="delete" role="tabpanel">
                <form method="post">
                    <input type="number" name="id" class="form-control mb-3" placeholder="Data Entry ID" required >
                    <button type="submit" name="delete" class="btn btn-style w-100">Delete</button>
                </form>
            </div>

        </div>
    </div>


        <!-- Search Bar -->
        <div class="card p-3 mb-4 w-100" style="background-color: #acb39aff; max-width: 1100px; ">
            <h5 class="fw-bold" style="color: #853354ff; font-family: 'Cal Sans', sans-serif;">SEARCH BAR</h5>
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
                        <button type="submit" class="btn btn-custom" style="background-color: #b05377; color: white;">Search</button>
                        <button type="submit" name="export_filtered" class="btn btn-custom" style="background-color: #7d0532; color: white;">Export Filtered</button>
                        <button type="submit" name="export_all" class="btn btn-custom" style="background-color: #102c20; color: white;">Export All</button>
                        <button type="submit" name="view_all" class="btn btn-custom" style="background-color: #4f6914; color: white;">View All</button>
                    </div>
                </div>
            </form>
        </div>

<?php if ($show_all) { ?>
        <!-- All Employees -->
        <h4 class="fw-bold" style="text-align: left; padding-left: 90px;">All Employees</h4>
        <div class="table-container">
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
        <h4 class="fw-bold" style="text-align: left; padding-left: 90px;">Filtered Employees</h4>
        <div class="table-container mb-4">
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
