<?php
include 'db.php';

// Start or resume a session to access $_SESSION superglobal
session_start();

// Retrieve email, first name, and last name from session
$email = $_SESSION['email'];

$sql = "SELECT first_name, last_name FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the result row
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];

    // Use the retrieved first name and last name
    echo "First Name: $first_name<br>";
    echo "Last Name: $last_name<br>";
} else {
    echo "No user found with this email.";
}
$sql_check_booking = "SELECT * FROM booking WHERE email = ?";
$stmt_check_booking = $conn->prepare($sql_check_booking);
$stmt_check_booking->bind_param("s", $email);
$stmt_check_booking->execute();
$result_check_booking = $stmt_check_booking->get_result();

if ($result_check_booking->num_rows > 0) {
    header("Location: dashboard.html");
    echo "You have already booked. Please check your bookings.";
    exit();
}

// Retrieve other booking details from the form submission
$arrivalDate = $_POST['arrival-date'];
$departureDate = $_POST['departure-date'];
$gender = $_POST['gender'];
$roomNumber = $_POST['room-number'];
$guestNumber = $_POST['num-guests'];
$bedPreference = $_POST['bed-preference'];
$foodPreference = $_POST['food'];
$price = 0;

// Convert date format to 'YYYY-MM-DD' (MySQL format)
$arrivalDateFormatted = date('Y-m-d', strtotime($arrivalDate));
$departureDateFormatted = date('Y-m-d', strtotime($departureDate));

// Calculate price based on bed preference
if ($bedPreference === "single") {
    $price += 5;
} elseif ($bedPreference === "double") {
    $price += 8;
}

// Calculate price based on food preference and number of guests
if ($foodPreference === "yes") {
    $price += ($guestNumber * 7); // $7 per guest
}

// Prepare and execute SQL query to insert booking details into the 'booking' table
$sql = "INSERT INTO booking (email, first_name, last_name, arrival_date, departure_date, gender, room_number, guest_number, bed_preference, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssi", $email, $first_name, $last_name, $arrivalDateFormatted, $departureDateFormatted, $gender, $roomNumber, $guestNumber, $bedPreference, $price);
$stmt->execute();

// Check if the insertion was successful
if ($stmt->affected_rows > 0) {
    echo "Booking successful! Total price: $" . $price;
    header("Location: mybooking.php");
} else {
    echo "Error in booking. Please try again.";
}
?>
