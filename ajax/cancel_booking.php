<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized.";
    exit;
}

$booking_id = (int)$_POST['booking_id'];

$qry = $dbconnc->prepare("SELECT event_id, quantity, total_amount FROM bookings WHERE id = ? AND user_id = ?");
$qry->execute([$booking_id, $_SESSION['user_id']]);
$booking = $qry->fetch();

if (!$booking) {
    echo "Booking not found.";
    exit;
}

$event_id = $booking['event_id'];
$currentQuantity = $booking['quantity'];
$currentAmount = $booking['total_amount'];

if (isset($_POST['quantity'])) {

    $quantityToCancel = (int)$_POST['quantity'];

    if ($quantityToCancel <= 0 || $quantityToCancel > $currentQuantity) {
        echo "Invalid number of seats to cancel.";
        exit;
    }

    $pricePerSeat = $currentAmount / $currentQuantity;
    $amountToReduce = $pricePerSeat * $quantityToCancel;
    $newQuantity = $currentQuantity - $quantityToCancel;
    $newAmount = $currentAmount - $amountToReduce;

    $qry = $dbconnc->prepare("UPDATE bookings SET quantity = ?, total_amount = ? WHERE id = ?");
    $qry->execute([$newQuantity, $newAmount, $booking_id]);

    $qry = $dbconnc->prepare("UPDATE events SET available_seats = available_seats + ? WHERE id = ?");
    $qry->execute([$quantityToCancel, $event_id]);

    echo "$quantityToCancel seat(s) cancelled successfully. ₹" . number_format($amountToReduce, 2) . " adjusted.";
} else {

    $qry = $dbconnc->prepare("UPDATE events SET available_seats = available_seats + ? WHERE id = ?");
    $qry->execute([$currentQuantity, $event_id]);

    $qry = $dbconnc->prepare("DELETE FROM bookings WHERE id = ?");
    $qry->execute([$booking_id]);

    echo "Booking cancelled successfully. ₹" . number_format($currentAmount, 2) . " refunded.";
}
?>
