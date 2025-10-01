<?php
$servername = "localhost";
$username   = "root";
$password   = ""; // no password in XAMPP
$dbname     = "employeedetails";

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
    $conn->query(query: $sql);
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
    $conn->query($sql);
}

// Delete Employee
if (isset($_POST["delete"])) {
    $id = $_POST["id"];
    $sql = "DELETE FROM employeedetails WHERE DataEntryID='$id'";
    $conn->query($sql);
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

$result_all = $conn->query($sql_all);
$result_filtered = $conn->query($sql_filtered);

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
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
        }
        table th {
            background: #006d77 !important;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        .btn-custom {
            border-radius: 6px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Employee Management System</span>
        </div>
    </nav>

    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Add Employee -->
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Add Employee</h5>
                    <form method="post">
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
                        <button type="submit" name="add" class="btn btn-success btn-custom w-100">➕ Add</button>
                    </form>
                </div>
            </div>

            <!-- Update Employee -->
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Update Employee</h5>
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
                        <button type="submit" name="update" class="btn btn-primary btn-custom w-100">✏️ Update</button>
                    </form>
                </div>
            </div>

            <!-- Delete Employee -->
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Delete Employee</h5>
                    <form method="post">
                        <input type="number" name="id" class="form-control mb-3" placeholder="Data Entry ID" required>
                        <button type="submit" name="delete" class="btn btn-danger btn-custom w-100">🗑 Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="card p-3 mb-4">
            <h5>Search Employees</h5>
            <form method="post" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name">
                </div>
                <div class="col-md-3">
                    <input type="date" name="shift_date" class="form-control">
                </div>
                <div class="col-md-2">
                    <input type="number" name="shift_no" class="form-control" placeholder="Shift No">
                </div>
                <div class="col-md-4 text-end">
                    <button type="submit" class="btn btn-dark btn-custom">🔍 Search</button>
                    <button type="submit" name="export_filtered" class="btn btn-warning btn-custom">Export Filtered</button>
                    <button type="submit" name="export_all" class="btn btn-secondary btn-custom">Export All</button>
                </div>
            </form>
        </div>

        <!-- Filtered Employees -->
        <h4>Filtered Employees</h4>
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

        <!-- All Employees -->
        <h4>All Employees</h4>
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
                    <?php } else { ?>
                        <tr><td colspan="7" class="text-center text-muted">No records found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
