<!------------------------------------------------- PHP PART ---------------------------------------------->
<?php
$servername = "localhost";
$username = "root";
$password = ""; // no password in XAMPP
$dbname = "employeedetails";

session_start();

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert New Employee
if (isset($_POST["add"])) {
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $shiftDate = $_POST["shift_date"];
    $shiftNo = $_POST['shift_no'];
    $hours = $_POST['hours'];
    $dutyType = $_POST['duty_type'];

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
    $id = $_POST["id"];
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $shiftDate = $_POST["shift_date"];
    $shiftNo = $_POST['shift_no'];
    $hours = $_POST['hours'];
    $dutyType = $_POST['duty_type'];

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
?>
<!DOCTYPE html>
<html>

<head>
    <title>Employee Management</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>

    <!-- SPLASH SCREEN -->
    <div id="splash-screen" class="d-flex justify-content-center align-items-center flex-column">
        <div class="spinner-border text-light mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
        <h2 class="text-light fw-bold">Employee Management System</h2>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-dark" style="background-color: #0D6EFD; padding: 15px 30px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span class="navbar-brand mb-0 h1 fw-bold">EMPLOYEE MANAGEMENT SYSTEM</span>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary" id="toggleDarkMode" title="Toggle dark/light mode">
                    <span id="darkModeIcon" class="material-icons">dark_mode</span>
                </button>
                <button class="btn btn-light" id="toggleSidebar">☰</button>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <!-- Google Material Icons CDN -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <div id="sidebar" class="bg-dark text-white p-3" style="width: 250px; min-height: 100vh; transition: all 0.3s;">
            <h5 class="fw-bold mb-3">MENU</h5>
            <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#add" class="nav-link text-white" data-bs-toggle="tab">
                <span class="material-icons align-middle">person_add</span> Add Employee
                </a>
            </li>
            <li class="nav-item">
                <a href="#update" class="nav-link text-white" data-bs-toggle="tab">
                <span class="material-icons align-middle">edit</span> Update Employee
                </a>
            </li>
            <li class="nav-item">
                <a href="#delete" class="nav-link text-white" data-bs-toggle="tab">
                <span class="material-icons align-middle">delete</span> Delete Employee
                </a>
            </li>
            <li class="nav-item">
                <a href="#search" class="nav-link text-white">
                <span class="material-icons align-middle">search</span> Search
                </a>
            </li>
            <li class="nav-item">
                <a href="#all" class="nav-link text-white">
                <span class="material-icons align-middle">list</span> View Employees
                </a>
            </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div id="content" class="flex-grow-1 p-4">
            <div class="tab-content">

                <!-- Add Form -->
                <div class="tab-pane fade" id="add" role="tabpanel">
                    <div class="card p-3 mb-4">
                        <h5 class="fw-bold mb-3">Add Employee</h5>
                        <form method="post">
                            <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name"
                                required>
                            <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name"
                                required>
                            <input type="date" name="shift_date" class="form-control mb-2" required>
                            <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No"
                                required>
                            <input type="number" name="hours" class="form-control mb-2" placeholder="Hours" required>
                            <select name="duty_type" class="form-select mb-2" required>
                                <option value="OnDuty">On Duty</option>
                                <option value="Late">Late</option>
                                <option value="Overtime">Overtime</option>
                            </select>
                            <button type="submit" name="add" class="btn btn-primary w-100">Add</button>
                        </form>
                    </div>
                </div>

                <!-- Update Form -->
                <div class="tab-pane fade" id="update" role="tabpanel">
                    <div class="card p-3 mb-4">
                        <h5 class="fw-bold mb-3">Update Employee</h5>
                        <form method="post">
                            <input type="number" name="id" class="form-control mb-2" placeholder="Data Entry ID"
                                required>
                            <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name"
                                required>
                            <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name"
                                required>
                            <input type="date" name="shift_date" class="form-control mb-2" required>
                            <input type="number" name="shift_no" class="form-control mb-2" placeholder="Shift No"
                                required>
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

                <!-- Delete Form -->
                <div class="tab-pane fade" id="delete" role="tabpanel">
                    <div class="card p-3 mb-4">
                        <h5 class="fw-bold mb-3">Delete Employee</h5>
                        <form method="post">
                            <input type="number" name="id" class="form-control mb-3" placeholder="Data Entry ID"
                                required>
                            <button type="submit" name="delete" class="btn btn-danger w-100">Delete</button>
                        </form>
                    </div>
                </div>

                <!-- Search (hidden by default, shown via sidebar) -->
                <div id="search" class="card p-3 mb-4" style="display:none;">
                    <h5 class="fw-bold">Search Employees</h5>
                    <form method="post" class="row g-3" id="employee-search-form">
                        <div class="col-md-4"><input type="text" name="last_name" class="form-control"
                                placeholder="Last Name"></div>
                        <div class="col-md-4"><input type="date" name="shift_date" class="form-control"></div>
                        <div class="col-md-4"><input type="number" name="shift_no" class="form-control"
                                placeholder="Shift No"></div>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="submit" class="btn btn-primary" id="search-btn">Search</button>
                            <button type="submit" name="export_filtered" class="btn btn-secondary">Export
                                Filtered</button>
                            <button type="submit" name="export_all" class="btn btn-dark">Export All</button>
                            <button type="submit" name="view_all" class="btn btn-success">View All</button>
                        </div>
                    </form>
                </div>

                <!-- Employees Table -->
                <div id="all">
                    <?php if ($show_all) { ?>
                        <h4 class="fw-bold">All Employees</h4>
                    <?php } else { ?>
                        <h4 class="fw-bold">Filtered Employees</h4>
                    <?php } ?>

                    <div class="table-scroll">
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
    <script>
    // Dark/Light mode toggle
    document.addEventListener('DOMContentLoaded', function() {
        var darkModeBtn = document.getElementById('toggleDarkMode');
        var darkModeIcon = document.getElementById('darkModeIcon');
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        var savedMode = localStorage.getItem('themeMode');
        var body = document.body;
        function setMode(mode) {
            if (mode === 'dark') {
                body.classList.add('dark-mode');
                darkModeIcon.textContent = 'light_mode';
            } else {
                body.classList.remove('dark-mode');
                darkModeIcon.textContent = 'dark_mode';
            }
        }
        setMode(savedMode ? savedMode : (prefersDark ? 'dark' : 'light'));
        darkModeBtn.addEventListener('click', function() {
            var isDark = body.classList.toggle('dark-mode');
            darkModeIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
            localStorage.setItem('themeMode', isDark ? 'dark' : 'light');
        });

        // Sidebar tab navigation and search card logic
        var searchCard = document.getElementById('search');
        var sidebarLinks = document.querySelectorAll('#sidebar .nav-link');
        var tabPanes = document.querySelectorAll('.tab-pane');
        var cards = document.querySelectorAll('.card.p-3.mb-4');

        sidebarLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var href = link.getAttribute('href');
                if (!href || !href.startsWith('#')) return;
                e.preventDefault();
                // Find the card or tab-pane to toggle
                var isSearch = href === '#search';
                var target = isSearch ? document.getElementById('search') : document.querySelector(href);
                var isCard = isSearch || (target && target.classList.contains('tab-pane'));
                // If already visible, hide it (toggle off)
                var isVisible = false;
                if (isSearch && target && target.style.display !== 'none') {
                    isVisible = true;
                } else if (!isSearch && target && target.classList.contains('show') && target.classList.contains('active')) {
                    isVisible = true;
                }
                if (isVisible) {
                    // Hide all
                    if (isSearch && target) {
                        target.style.display = 'none';
                    }
                    tabPanes.forEach(function(tab) {
                        tab.classList.remove('show', 'active');
                    });
                    cards.forEach(function(card) {
                        card.style.display = 'none';
                    });
                    return;
                }
                // Otherwise, show only the selected card/tab
                // Hide all
                if (isSearch) {
                    tabPanes.forEach(function(tab) {
                        tab.classList.remove('show', 'active');
                    });
                    cards.forEach(function(card) {
                        if(card !== target) card.style.display = 'none';
                    });
                    target.style.display = '';
                    target.scrollIntoView({behavior: 'smooth'});
                } else {
                    if (searchCard) searchCard.style.display = 'none';
                    tabPanes.forEach(function(tab) {
                        tab.classList.remove('show', 'active');
                    });
                    cards.forEach(function(card) {
                        if (card !== searchCard) {
                            if (card.closest(href)) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    });
                    target.classList.add('show', 'active');
                    // If View Employees, submit a hidden form to trigger 'view_all'
                    if (href === '#all') {
                        var form = document.createElement('form');
                        form.method = 'post';
                        form.style.display = 'none';
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'view_all';
                        input.value = '1';
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        target.scrollIntoView({behavior: 'smooth'});
                    }
                }
            });
        });
    });
    </script>
</body>

</html>