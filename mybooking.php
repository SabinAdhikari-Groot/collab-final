<?php
include 'db.php';

// Start or resume a session to access $_SESSION superglobal
session_start();

// Retrieve email from session
$email = $_SESSION['email'];

// Fetch booking details for the logged-in user
$sql = "SELECT * FROM booking WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="mybooking.css"> 
</head>
<body>
    <h1>My Bookings</h1>
    <table>
        <thead>
            <tr>
                <th>Arrival Date</th>
                <th>Departure Date</th>
                <th>Gender</th>
                <th>Room Number</th>
                <th>Number of Guests</th>
                <th>Bed Preference</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo $booking['arrival_date']; ?></td>
                    <td><?php echo $booking['departure_date']; ?></td>
                    <td><?php echo $booking['gender']; ?></td>
                    <td><?php echo $booking['room_number']; ?></td>
                    <td><?php echo $booking['guest_number']; ?></td>
                    <td><?php echo $booking['bed_preference']; ?></td>
                    <td>$<?php echo $booking['price']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <button onclick="backToHomepage()">Back</button>
        <button onclick="cancelBooking()">Cancel Booking</button>
        <button onclick="pay()">Pay</button>
    </div>
    <script src="mybooking.js"></script>
</body>
</html>
