<?php
include 'db.php'; // Database connection parameters

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adding staff
    if (isset($_POST["addstaff"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        // Validate email format
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format";
            exit();
        }

        // Check if email ends with @gmail.com
        if (substr($username, -10) !== "@gmail.com") {
            echo "Email must end with @gmail.com";
            exit();
        }

        // Check if email is unique
        $check_email_query = "SELECT * FROM stafflogin WHERE username='$username'";
        $check_email_result = $conn->query($check_email_query);
        if ($check_email_result->num_rows > 0) {
            echo "Email already exists";
            exit();
        }

        // Check if password follows specific rules
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            echo "Password must contain at least one uppercase letter, one lowercase letter, one number, one special character, and be at least 8 characters long";
            exit();
        }
        $sql = "INSERT INTO stafflogin (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "New staff account created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Editing staff
    if (isset($_POST["editstaff"])) {
        $id = $_POST["id"];
        $newUsername = $_POST["newusername"];
        $newPassword = $_POST["newpassword"];
        $updateFields = [];
        
        // Check if fields are not empty and add them to updateFields array
        if (!empty($newUsername)) {
            $updateFields[] = "username='$newUsername'";
        }
        if (!empty($newPassword)) {
            $updateFields[] = "password='$newPassword'";
        }
        
        // Construct the SQL query based on the fields to update
        $updateQuery = "UPDATE stafflogin SET " . implode(", ", $updateFields) . " WHERE id=$id";
        
        if (!empty($updateFields)) { // Check if there are fields to update
            if ($conn->query($updateQuery) === TRUE) {
                echo "Staff account updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }

    // Deleting staff
    if (isset($_POST["deletestaff"])) {
        $id = $_POST["id"];
        $sql = "DELETE FROM stafflogin WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "Staff account deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// Function to fetch booking information
function fetchTransactionDetails() {
    global $conn;
    $sql = "SELECT * FROM payment_details";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["username"] . "</td>";
            echo "<td>" . $row["payment_id"] . "</td>";
            echo "<td>" . $row["payment_date"] . "</td>";
            echo "<td>$ " . $row["price"] . "</td>";
            echo "<td>" . $row["payment_status"] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No bookings found</td></tr>";
    }
}

function fetchBookingInformation() {
    global $conn;
    $sql = "SELECT * FROM booking";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["arrival_date"] . "</td>";
            echo "<td>" . $row["departure_date"] . "</td>";
            echo "<td>$ " . $row["price"] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No bookings found</td></tr>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <script>
        function showEditFields(id) {
            var editFields = document.getElementById('editFields_' + id);
            editFields.style.display = 'block';
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo"><img src="./images/logo.png" alt="Logo"></h1>
            <ul class="nav-links">
                <li><a href="newLogin.html">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <section class="manage-staff">
            <h2>Manage Staff Accounts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM stafflogin";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["username"] . "</td>";
                            echo "<td>" . $row["password"] . "</td>";
                            echo "<td>";
                            echo "<button onclick='showEditFields(" . $row["id"] . ")'>Edit</button>";
                            echo "<form method='post' action='".$_SERVER['PHP_SELF']."' id='editFields_" . $row["id"] . "' style='display: none;'>";
                            echo "<input type='hidden' name='id' value='".$row["id"]."'>";
                            echo "<input type='text' name='newusername' placeholder='New Username'>";
                            echo "<input type='password' name='newpassword' placeholder='New Password'>";
                            echo "<button type='submit' name='editstaff'>Save</button>";
                            echo "</form>";
                            echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
                            echo "<input type='hidden' name='id' value='".$row["id"]."'>";
                            echo "<button type='submit' name='deletestaff'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>0 results</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
        <br>
        <!-- Add staff form -->
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <label for="username">Email:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="addstaff">Add Staff</button>
        </form>
    </div>
    <div class="container">
    <section class="manage-users">
        <h2>Manage User Accounts</h2>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_users = "SELECT * FROM users";
                $result_users = $conn->query($sql_users);

                if ($result_users->num_rows > 0) {
                    while($row_users = $result_users->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_users["id"] . "</td>";
                        echo "<td>" . $row_users["first_name"] . "</td>";
                        echo "<td>" . $row_users["last_name"] . "</td>";
                        echo "<td>" . $row_users["phone_number"] . "</td>";
                        echo "<td>" . $row_users["email"] . "</td>";
                        echo "<td>" . $row_users["password"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</div>
    <div class="container">
        <section class="booking-information">
            <h2>Booking Information</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Arrival Date</th>
                        <th>Departure date</th>
                        <th>price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php fetchBookingInformation(); ?>
                </tbody>
            </table>
        </section>
    </div>
    <div class="container">
        <!-- Transaction details section -->
        <section class="transaction-details">
            <h2>Transaction Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Paid Id</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php fetchTransactionDetails(); ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>

<?php
$conn->close();
?>
