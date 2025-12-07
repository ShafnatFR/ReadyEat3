<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Ready for Pickup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 600;
            color: #6b7280;
        }

        .value {
            color: #111827;
        }

        .highlight-box {
            background: #dcfce7;
            border: 2px solid #10b981;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }

        .checklist {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .checklist li {
            padding: 8px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>✅ Your Order is Ready!</h1>
            <p>Time to pick up your delicious food</p>
        </div>

        <div class="content">
            <p>Hi <strong>{{ $order->customer_name }}</strong>,</p>

            <p>Great news! Your order has been prepared and is ready for pickup.</p>

            <div class="highlight-box">
                <h2 style="margin: 0; color: #10b981;">
                    {{ \Carbon\Carbon::parse($order->pickup_date)->format('l, d F Y') }}</h2>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #6b7280;">Pickup Date</p>
            </div>

            <div class="order-details">
                <div class="detail-row">
                    <span class="label">Invoice Number:</span>
                    <span class="value"><strong>{{ $order->invoice_code }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="label">Total Amount:</span>
                    <span class="value"><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="label">Status:</span>
                    <span class="value">
                        <span
                            style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            Ready for Pickup
                        </span>
                    </span>
                </div>
            </div>

            <div class="checklist">
                <h3>📋 Before You Pick Up:</h3>
                <ul style="list-style: none; padding: 0;">
                    <li>✓ Bring your invoice number: <strong>{{ $order->invoice_code }}</strong></li>
                    <li>✓ Check pickup date and time</li>
                    <li>✓ Prepare payment if not yet paid</li>
                    <li>✓ Contact us if you need to reschedule</li>
                </ul>
            </div>

            @if($order->admin_note)
                <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <strong>📝 Note from Admin:</strong><br>
                    {{ $order->admin_note }}
                </div>
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <h3>Pickup Location:</h3>
                <p><strong>ReadyEat Store</strong><br>
                    Jl. Contoh No. 123, Jakarta<br>
                    Open: 09:00 - 20:00</p>

                <p style="margin-top: 20px;">
                    <strong>Need to contact us?</strong><br>
                    Email: support@readyeat.com<br>
                    Phone: 081234567890
                </p>
            </div>

            <div style="background: #fee2e2; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;">
                <strong>⚠️ Important:</strong><br>
                Please pick up your order on the scheduled date. Late pickups may affect food quality.
            </div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} ReadyEat. All rights reserved.</p>
            <p style="font-size: 12px;">This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>

</html>