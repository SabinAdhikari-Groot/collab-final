<?php
include 'db.php'; // Database connection parameters

// Function to fetch payment details
function fetchPaymentDetails() {
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
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No payment details found</td></tr>";
    }
}

// Function to process payment
function staffProcessPayment($email) {
    global $conn;

    // Fetch booking details for the user
    $sql = "SELECT * FROM booking WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Assuming there's only one booking per user for simplicity
    $booking = $result->fetch_assoc();
    $bookingId = $booking['id'];
    $price = $booking['price'];

    // Insert payment details into payment_details table
    $paymentStatus = 'done';
    $paymentDate = date('Y-m-d'); // Current date

    $insertSql = "INSERT INTO payment_details (booking_id, username, payment_status, payment_date, price) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("issss", $bookingId, $email, $paymentStatus, $paymentDate, $price);
    $insertStmt->execute();

    // Redirect back to manage_payment.php after payment
    header("Location: manage_payment.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payment</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo"><img src="./images/logo.png" alt="Logo"></h1>
            <ul class="nav-links">
                <li><a href="staff_booking.html">Book-Beds</a></li>
                <li><a href="staff_dashboard.php">Dashboard</a></li>
                <li><a href="newLogin.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <section class="payment-details">
            <h2>Payment Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Paid ID</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php fetchPaymentDetails(); ?>
                </tbody>
            </table>
        </section>
    </div>
    <div class="container">
        <section class="make-payment">
            <h2>Make Payment</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Payment Status</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch users who haven't paid yet
                    $sql = "SELECT * FROM booking WHERE email NOT IN (SELECT username FROM payment_details)";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>Pending</td>";
                            echo "<td>$" . $row["price"] . "</td>";
                            echo "<td>";
                            echo "<form method='POST' action=''>";
                            echo "<input type='hidden' name='email' value='" . $row["email"] . "'>";
                            echo "<button type='submit' name='pay'>Pay</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No pending payments</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>

<?php
// Check if the Pay button is clicked
if (isset($_POST['pay'])) {
    $email = $_POST['email'];
    staffProcessPayment($email);
}
$conn->close();
?>
