<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = (int) $_POST['event_id'];
$quantity = max(1, (int) $_POST['quantity']); 


$qry = $dbconnc->prepare("SELECT id, quantity FROM bookings WHERE user_id = ? AND event_id = ?");
$qry->execute([$user_id, $event_id]);
$existing_booking = $qry->fetch();


$qry = $dbconnc->prepare("SELECT available_seats, price FROM events WHERE id = ?");
$qry->execute([$event_id]);
$event = $qry->fetch();

if (!$event) {
    echo "Event not found.";
    exit;
}

$current_available = (int) $event['available_seats'];
$price = (float) $event['price'];


$previous_quantity = $existing_booking ? (int) $existing_booking['quantity'] : 0;
$total_quantity = $previous_quantity + $quantity; 

if ($total_quantity > $current_available) {
    echo "Only $current_available seats available.";
    exit;
}

$total_amount = $total_quantity * $price;

try {
    $dbconnc->beginTransaction();

    if ($existing_booking) {

        $qry = $dbconnc->prepare("UPDATE bookings SET quantity = ?, total_amount = ? WHERE id = ?");
        $qry->execute([$total_quantity, $total_amount, $existing_booking['id']]);
    } else {

        $qry = $dbconnc->prepare("INSERT INTO bookings (user_id, event_id, quantity, total_amount) VALUES (?, ?, ?, ?)");
        $qry->execute([$user_id, $event_id, $quantity, $total_amount]);
    }


    $qry = $dbconnc->prepare("UPDATE events SET available_seats = available_seats - ? WHERE id = ?");
    $qry->execute([$quantity, $event_id]);

    $dbconnc->commit();
    echo $existing_booking ? "Booking updated successfully!" : "Booked successfully!";
} catch (Exception $e) {
    $dbconnc->rollBack();
    echo "Error: " . $e->getMessage();
}
