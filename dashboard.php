<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$qry = $dbconnc->query("SELECT * FROM events");
$events = $qry->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Book Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/style.css" rel="stylesheet">
    <style>
        input[type="number"] {
            width: 50px;
            padding: 4px;
        }

        .event {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }

        .message {
            color: green;
        }
    </style>

    <script>
        function bookTicket(eventId) {
            const qtyInput = document.getElementById('qty-' + eventId);
            let quantity = parseInt(qtyInput.value);
            if (quantity < 1) quantity = 1;

            const existingQty = document.getElementById('existing-qty-' + eventId) ? parseInt(document.getElementById('existing-qty-' + eventId).innerText) : 0;

            if (existingQty > 0) {
                const confirmMsg = confirm(`You already have ${existingQty} tickets for this event. Do you want to add ${quantity} more?`);
                if (!confirmMsg) return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/book_ticket.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                document.getElementById('msg-' + eventId).innerHTML = this.responseText;
                if (this.responseText.includes('successfully')) {
                    setTimeout(() => location.reload(), 1000);
                }
            };

            xhr.send("event_id=" + eventId + "&quantity=" + quantity);
        }

        function calculateAmount(eventId) {
            const qtyInput = document.getElementById('qty-' + eventId);
            const pricePerSeat = parseFloat(document.getElementById('price-' + eventId).innerText);
            let quantity = parseInt(qtyInput.value);
            if (quantity < 1) quantity = 1;
            const totalAmount = quantity * pricePerSeat;
            document.getElementById('total-amount-' + eventId).innerText = "Total Amount: ₹" + totalAmount.toFixed(2);
        }
    </script>
</head>

<body class="dashboard-page">
    <section>
        <div class="header">
            <div><strong>Welcome, <?php echo $_SESSION['username']; ?></strong></div>
            <div>
                <a href="bookings.php">My Bookings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="container">
            <h3>Available Events</h3>
            <?php foreach ($events as $event): ?>
                <div class="event">
                    <strong><?= htmlspecialchars($event['name']) ?></strong>
                    <p>Date: <?= $event['date'] ?></p>
                    <p>Venue: <?= htmlspecialchars($event['venue']) ?></p>
                    <p>Available Seats: <?= $event['available_seats'] ?></p>
                    <p>Price per Seat: ₹<span id="price-<?= $event['id'] ?>"><?= $event['price'] ?></span></p>
                    <label>Quantity:
                        <input type="number" id="qty-<?= $event['id'] ?>" value="1" min="1"
                            onchange="calculateAmount(<?= $event['id'] ?>)">
                    </label>
                    <?php

                    $qry = $dbconnc->prepare("SELECT quantity FROM bookings WHERE user_id = ? AND event_id = ?");
                    $qry->execute([$_SESSION['user_id'], $event['id']]);
                    $existing_booking = $qry->fetch();
                    $existing_qty = $existing_booking ? $existing_booking['quantity'] : 0;
                    ?>

                    <p id="existing-qty-<?= $event['id'] ?>" style="color: blue;">Your Current Booking: <?= $existing_qty ?>
                    </p>



                    <p id="total-amount-<?= $event['id'] ?>">Total Amount: ₹<?= $existing_qty * $event['price'] ?></p>

                    <button onclick="bookTicket(<?= $event['id'] ?>)">Book / Add</button>
                    <p id="msg-<?= $event['id'] ?>" class="message"></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</body>

</html>