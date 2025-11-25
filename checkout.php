<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : 0;
$tickets_param = isset($_GET['tickets']) ? $_GET['tickets'] : '';

// Get event details
$event_sql = "SELECT * FROM events WHERE id = $event_id";
$event_result = mysqli_query($conn, $event_sql);
$event = mysqli_fetch_assoc($event_result);

// Parse ticket selections from URL parameter
$selected_tickets = [];
$subtotal = 0;

if ($tickets_param) {
    $ticket_pairs = explode(',', $tickets_param);
    foreach ($ticket_pairs as $pair) {
        $parts = explode(':', $pair);
        if (count($parts) == 2) {
            $ticket_id = $parts[0];
            $quantity = $parts[1];
            
            if ($quantity > 0) {
                $ticket_sql = "SELECT * FROM ticket_types WHERE id = $ticket_id";
                $ticket_result = mysqli_query($conn, $ticket_sql);
                $ticket = mysqli_fetch_assoc($ticket_result);
                
                if ($ticket) {
                    $selected_tickets[] = [
                        'id' => $ticket['id'],
                        'name' => $ticket['ticket_name'],
                        'price' => $ticket['price'],
                        'quantity' => $quantity,
                        'total' => $ticket['price'] * $quantity
                    ];
                    $subtotal += $ticket['price'] * $quantity;
                }
            }
        }
    }
}

$service_fee = 10;
$total = $subtotal + $service_fee;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    
    // Create order record
    $user_id = $_SESSION['user_id'];
    
    // Set initial status based on payment method
    $status = ($payment_method == 'esewa') ? 'pending' : 'completed';
    $payment_status = ($payment_method == 'esewa') ? 'pending' : 'paid';
    
    $order_sql = "INSERT INTO orders (user_id, event_id, subtotal, service_fee, total, payment_method, payment_status, status) 
                  VALUES ($user_id, $event_id, $subtotal, $service_fee, $total, '$payment_method', '$payment_status', '$status')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($selected_tickets as $ticket) {
            $item_sql = "INSERT INTO order_items (order_id, ticket_type_id, quantity, price) 
                        VALUES ($order_id, {$ticket['id']}, {$ticket['quantity']}, {$ticket['price']})";
            mysqli_query($conn, $item_sql);
        }
        
        // If eSewa payment, redirect to eSewa payment page
        if ($payment_method == 'esewa') {
            header("Location: esewa_payment.php?order_id=" . $order_id);
            exit();
        } else {
            // For card payment, update sold count and redirect to confirmation
            foreach ($selected_tickets as $ticket) {
                $update_sql = "UPDATE ticket_types SET sold = sold + {$ticket['quantity']} WHERE id = {$ticket['id']}";
                mysqli_query($conn, $update_sql);
            }
            header("Location: order_confirmation.php?order_id=" . $order_id);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo $event['title']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
        }
        
        .navbar {
            background: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .logo-icon {
            width: 32px;
            height: 32px;
            background: #4F46E5;
            border-radius: 6px;
            margin-right: 10px;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .checkout-form {
            background: white;
            border-radius: 12px;
            padding: 30px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        input, select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #4F46E5;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-option {
            border: 2px solid #D1D5DB;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .payment-option:hover {
            border-color: #4F46E5;
        }

        .payment-option.selected {
            border-color: #4F46E5;
            background: #EEF2FF;
        }

        .payment-option input[type="radio"] {
            width: auto;
            margin: 0;
        }

        .payment-logo {
            height: 30px;
            font-weight: 700;
            font-size: 18px;
        }

        .esewa-logo {
            color: #60bb46;
        }

        .card-logo {
            color: #4F46E5;
        }

        .card-fields {
            display: none;
        }

        .card-fields.active {
            display: block;
        }
        
        .order-summary {
            background: white;
            border-radius: 12px;
            padding: 30px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        
        .event-info {
            padding-bottom: 20px;
            border-bottom: 1px solid #E5E7EB;
            margin-bottom: 20px;
        }
        
        .event-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .event-date {
            font-size: 14px;
            color: #6B7280;
        }
        
        .ticket-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .price-total {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            font-size: 18px;
            font-weight: 700;
            margin-top: 15px;
        }
        
        .btn-complete {
            width: 100%;
            background: #4F46E5;
            color: white;
            padding: 16px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        
        .btn-complete:hover {
            background: #4338CA;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon"></div>
            Ticketly
        </a>
    </nav>
    
    <div class="container">
        <div class="checkout-form">
            <h2 class="section-title">Billing Information</h2>
            
            <form method="POST" id="checkoutForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo $_SESSION['user_email']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" required>
                </div>
                
                <h2 class="section-title" style="margin-top: 30px;">Payment Method</h2>
                
                <div class="payment-methods">
                    <div class="payment-option selected" onclick="selectPayment('esewa', this)">
                        <input type="radio" name="payment_method" value="esewa" checked>
                        <div class="payment-logo esewa-logo">eSewa</div>
                    </div>
                    <div class="payment-option" onclick="selectPayment('card', this)">
                        <input type="radio" name="payment_method" value="card">
                        <div class="payment-logo card-logo">Card</div>
                    </div>
                </div>

                <div id="cardFields" class="card-fields">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" id="cardNumber">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" name="expiry" placeholder="MM/YY" id="cardExpiry">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" name="cvv" placeholder="123" id="cardCVV">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-complete">Complete Purchase</button>
            </form>
        </div>
        
        <div class="order-summary">
            <h3 class="section-title">Order Summary</h3>
            
            <div class="event-info">
                <div class="event-title"><?php echo $event['title']; ?></div>
                <div class="event-date">
                    <?php echo date('D, M j, Y • g:i A', strtotime($event['start_date'])); ?>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <?php foreach ($selected_tickets as $ticket): ?>
                    <div class="ticket-item">
                        <span><?php echo htmlspecialchars($ticket['name']); ?> × <?php echo $ticket['quantity']; ?></span>
                        <span>Rs <?php echo number_format($ticket['total'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="price-row">
                <span style="color: #6B7280;">Subtotal</span>
                <span style="font-weight: 600;">Rs <?php echo number_format($subtotal, 2); ?></span>
            </div>
            
            <div class="price-row">
                <span style="color: #6B7280;">Service Fee(Rs 10)</span>
                <span style="font-weight: 600;">Rs 10</span>
            </div>
            
            <div class="price-total">
                <span>Total</span>
                <span>Rs <?php echo number_format($total, 2); ?></span>
            </div>
        </div>
    </div>

    <script>
        function selectPayment(method, element) {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            element.classList.add('selected');
            
            // Check the radio button
            element.querySelector('input[type="radio"]').checked = true;
            
            // Show/hide card fields
            const cardFields = document.getElementById('cardFields');
            const cardNumber = document.getElementById('cardNumber');
            const cardExpiry = document.getElementById('cardExpiry');
            const cardCVV = document.getElementById('cardCVV');
            
            if (method === 'card') {
                cardFields.classList.add('active');
                cardNumber.required = true;
                cardExpiry.required = true;
                cardCVV.required = true;
            } else {
                cardFields.classList.remove('active');
                cardNumber.required = false;
                cardExpiry.required = false;
                cardCVV.required = false;
            }
        }
    </script>
</body>
</html>