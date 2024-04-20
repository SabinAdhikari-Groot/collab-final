function backToHomepage() {
    window.location.href = "nhomepage.html";
}

function pay() {
    window.location.href = "payforbooking.html";
}

function cancelBooking() {
    if (confirm("Are you sure you want to cancel this booking?")) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert("Booking cancelled successfully.");
                    location.reload();
                } else {
                    alert("Error cancelling booking.");
                }
            }
        };
        xhr.open("POST", "cancel_booking.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send();
    }
}