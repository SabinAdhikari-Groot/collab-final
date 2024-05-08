<?php
include 'db.php'; // Include your database connection parameters

// Function to fetch guest information from the booking table
function fetchGuestInformation() {
    global $conn;
    $sql = "SELECT booking.*, payment_details.payment_status 
            FROM booking 
            LEFT JOIN payment_details ON booking.id = payment_details.booking_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["first_name"] . "</td>";
            echo "<td>" . $row["last_name"] . "</td>";
            echo "<td>" . $row["gender"] . "</td>";
            echo "<td>" . $row["arrival_date"] . "</td>";
            echo "<td>" . $row["departure_date"] . "</td>";
            echo "<td>" . $row["room_number"] . "</td>";
            echo "<td>" . $row["bed_preference"] . "</td>";
            echo "<td>$" . $row["price"] . "</td>";
            echo "<td>" . ($row["payment_status"] ?? "Pending") . "</td>"; // Display "Pending" if payment status is not available
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No guest information found</td></tr>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="staff_dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo"><img src="./images/logo.png" alt="Logo"></h1>
            <ul class="nav-links">
                <li><a href="staff_booking.html">Book-Beds</a></li>
                <li><a href="manage_payment.php">Manage payment</a></li>
                <li><a href="newLogin.html">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <section class="guest-info">
            <h2>Guest Information</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Gender</th>
                        <th>Arrival Date</th>
                        <th>Departure Date</th>
                        <th>Room Number</th>
                        <th>Bed type</th>
                        <th>Price</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php fetchGuestInformation(); ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
