<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout</title>
</head>

<body>

    <h1>Checkout</h1>
    <h2>Order Summary</h2>

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

        @foreach ($cart as $item)

            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td>₱{{ $item['selling_price'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>₱{{ $item['selling_price'] * $item['quantity'] }}</td>
            </tr>

        @endforeach

        <tr>
            <td colspan="3"><strong>Grand Total</strong></td>
            <td><strong>₱{{ $total }}</strong></td>
        </tr>

    </tbody>

</table>


    <form action="{{ route('checkout.process') }}" method="POST">

        @csrf
        <label for="amount_paid">Amount Paid</label>
        <input type="number" name="amount_paid" id="amount_paid" min="{{ $total }}" step="0.01" required>
        <button type="submit">Confirm Payment</button>

    </form>

    <br>
    <a href="{{ route('pos') }}"><button type="button">Back to POS</button></a>

</body>

</html>