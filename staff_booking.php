<?php
include 'db.php'; // Include your database connection parameters

// Function to delete booking records for departures till today
function deletePastBookings() {
    global $conn;
    $today = date("Y-m-d");
    $sql = "DELETE FROM booking WHERE departure_date <= '$today'";
    $result = $conn->query($sql);
    if (!$result) {
        echo "Error deleting past bookings: " . $conn->error;
        exit();
    }
}

// Call function to delete past bookings
deletePastBookings();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST["email"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $arrivalDate = $_POST["arrivalDate"];
    $departureDate = $_POST["departureDate"];
    $gender = $_POST["gender"];
    $roomNumber = $_POST["room-number"];
    $numOfGuest = $_POST["numOfGuest"];
    $bedPreference = $_POST["bedPreference"];
    $foodPreference = $_POST['food'];
    $price = 0;

    // Validate arrival date is not before today
    $today = date("Y-m-d");
    if ($arrivalDate < $today) {
        echo "Arrival date cannot be before today.";
        exit();
    }

    // Validate departure date is at least 2 days after arrival date
    $arrivalDateTime = new DateTime($arrivalDate);
    $departureDateTime = new DateTime($departureDate);
    $interval = $arrivalDateTime->diff($departureDateTime);
    $daysDifference = $interval->format("%a");
    if ($daysDifference < 2) {
        echo "Departure date must be at least 2 days after arrival date.";
        exit();
    }

    // Check if room has capacity for additional guests
    $sql_check_room_capacity = "SELECT SUM(guest_number) AS total_guests FROM booking WHERE room_number = '$roomNumber'";
    $result_check_room_capacity = $conn->query($sql_check_room_capacity);
    $row_check_room_capacity = $result_check_room_capacity->fetch_assoc();
    $totalGuestsInRoom = $row_check_room_capacity['total_guests'];
    if (($totalGuestsInRoom + $numOfGuest) > 12) {
        echo "Room $roomNumber cannot accommodate more than 12 guests at a time.";
        exit();
    }

    // Calculate price based on bed preference and number of days
    if ($bedPreference === "single") {
        $price += 5 * $daysDifference;
    } elseif ($bedPreference === "double") {
        $price += 8 * $daysDifference;
    }

    // Calculate price based on food preference and number of guests
    if ($foodPreference === "yes") {
        $price += ($numOfGuest * 7 * $daysDifference); // $7 per guest per day
    }

    // Insert data into the booking table
    $sql = "INSERT INTO booking (email, first_name, last_name, arrival_date, departure_date, gender, room_number, guest_number, bed_preference, price) VALUES ('$email', '$firstName', '$lastName', '$arrivalDate', '$departureDate', '$gender', '$roomNumber', '$numOfGuest', '$bedPreference', '$price')";

    if ($conn->query($sql) === TRUE) {
        echo "Booking successful";
        header("Location: staff_dashboard.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close(); // Close the database connection
?>
