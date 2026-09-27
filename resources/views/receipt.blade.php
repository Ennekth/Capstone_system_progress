<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Receipt</title>
</head>

<body>

    <h1>Receipt</h1>
    <p>Transaction ID: {{ $transaction->transaction_id }}</p>
    <p>Date: {{ $transaction->created_at }}</p>

    <table border="1" cellpadding="10" cellspacing="0">

        <thead>

            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>

        </thead>

        <tbody>

            @foreach ($transaction->transactionItems as $item)

                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>₱{{ $item->selling_price }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₱{{ $item->subtotal }}</td>
                </tr>

            @endforeach

        </tbody>

    </table>

    <br>

    <p><strong>Total: ₱{{ $transaction->total_amount }}</strong></p>
    <p>Amount Paid: ₱{{ $transaction->amount_paid }}</p>
    <p>Change: ₱{{ $transaction->amount_change }}</p>

    <br>

    <a href="{{ route('pos') }}"><button type="button">Back to POS</button></a>

</body>

</html>