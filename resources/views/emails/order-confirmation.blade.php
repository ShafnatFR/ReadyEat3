<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
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
            background: linear-gradient(135deg, #F97316 0%, #FB923C 100%);
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

        .items-table {
            width: 100%;
            margin: 20px 0;
        }

        .items-table th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .total {
            font-size: 24px;
            font-weight: bold;
            color: #F97316;
            text-align: right;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            background: #F97316;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }

        .alert {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Order Confirmed!</h1>
            <p>Thank you for your order</p>
        </div>

        <div class="content">
            <p>Hi <strong>{{ $order->customer_name }}</strong>,</p>

            <p>Your order has been received and is being processed. Here are your order details:</p>

            <div class="order-details">
                <div class="detail-row">
                    <span class="label">Invoice Number:</span>
                    <span class="value"><strong>{{ $order->invoice_code }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="label">Order Date:</span>
                    <span class="value">{{ $order->created_at->format('d F Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Pickup Date:</span>
                    <span
                        class="value"><strong>{{ \Carbon\Carbon::parse($order->pickup_date)->format('d F Y') }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="label">Status:</span>
                    <span class="value">
                        <span
                            style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </span>
                </div>
            </div>

            <h3>Order Items:</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->menu->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($order->notes)
                <div class="alert">
                    <strong>Your Notes:</strong><br>
                    {{ $order->notes }}
                </div>
            @endif

            <div class="total">
                Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
            </div>

            <div class="alert">
                <strong>⏳ Next Steps:</strong><br>
                • Your payment is being verified by our team<br>
                • You will receive another email once approved<br>
                • Please bring your invoice code when picking up<br>
                • Contact us if you have any questions
            </div>

            <div style="text-align: center;">
                <p>Need help? Contact us:</p>
                <p><strong>Email:</strong> support@readyeat.com<br>
                    <strong>Phone:</strong> 081234567890
                </p>
            </div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} ReadyEat. All rights reserved.</p>
            <p style="font-size: 12px;">This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>

</html>