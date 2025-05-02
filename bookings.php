<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$totalStmt = $dbconnc->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
$totalStmt->execute([$_SESSION['user_id']]);
$totalBookings = $totalStmt->fetchColumn();
$totalPages = ceil($totalBookings / $limit);

$qry = $dbconnc->prepare("
    SELECT b.id AS booking_id, b.booked_at, e.name, e.date, e.venue, b.quantity, b.total_amount
    FROM bookings b
    JOIN events e ON b.event_id = e.id
    WHERE b.user_id = ?
    ORDER BY b.booked_at DESC
    LIMIT $limit OFFSET $offset
");
$qry->execute([$_SESSION['user_id']]);
$bookings = $qry->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
function cancelBooking(bookingId) {
    if (!confirm("Are you sure you want to cancel this booking?")) return;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/cancel_booking.php", true); 
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        alert(this.responseText);
        location.reload(); 
    };

    xhr.send("booking_id=" + bookingId);
}

function cancelPartialBooking(bookingId, currentQuantity) {
    let quantityToCancel = prompt("Enter the number of seats to cancel (Max: " + currentQuantity + ")");
    if (quantityToCancel == null || quantityToCancel <= 0 || quantityToCancel > currentQuantity) {
        alert("Invalid number of seats to cancel.");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/cancel_booking.php", true); 
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        alert(this.responseText);
        location.reload(); 
    };

    xhr.send("booking_id=" + bookingId + "&quantity=" + quantityToCancel);
}

    </script>
</head>
<body class="bookings-page">
<section>
        <div class="header">
            <div><strong>Welcome, <?php echo $_SESSION['username']; ?></strong></div>
            <div>
            <a href="dashboard.php">Dashboard</a> 
                <a href="logout.php">Logout</a>
            </div>
        </div>

    <div class="container">
        <h3>Booking History</h3>

        <?php if (count($bookings) > 0): ?>
            <?php foreach ($bookings as $b): ?>
                <div class="booking-item">
                    <strong><?= htmlspecialchars($b['name']) ?></strong>
                    <p>Date: <?= $b['date'] ?></p>
                    <p>Quantity: <?= $b['quantity'] ?></p>
                    <p>Total Amount: <?= $b['total_amount'] ?></p>
                    <p>Venue: <?= htmlspecialchars($b['venue']) ?></p>
                    <p>Booked At: <?= $b['booked_at'] ?></p>
                    <button onclick="cancelBooking(<?= $b['booking_id'] ?>)">Cancel Full Booking</button>
                    <button onclick="cancelPartialBooking(<?= $b['booking_id'] ?>, <?= $b['quantity'] ?>)">Cancel Some Seats</button>
                </div>
            <?php endforeach; ?>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
                <?php endif; ?>

                Page <?= $page ?> of <?= $totalPages ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <p class="message">No bookings yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
