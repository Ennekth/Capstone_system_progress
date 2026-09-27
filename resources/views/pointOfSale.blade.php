@extends('layouts.default')
@section('title') POS @endsection
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            margin-bottom: 20px;
        }

        .pos-container {
            display: flex;
            gap: 20px;
        }

        .products {
            flex: 2;
            background-color: white;
            padding: 20px;
            border: 2px solid black;
        }

        .cart {
            flex: 1;
            background-color: white;
            padding: 20px;
            border: 2px solid black;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .product {
            border: 1px solid #ccc;
            padding: 15px;
        }

        .product h3 {
            margin-top: 0;
        }

        .price {
            font-weight: bold;
        }

        button {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            background-color: black;
            color: white;
        }

        button:hover {
            background-color: #333;
        }

        .cart-item {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        .checkout {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background-color: green;
        }
    </style>
<body>
    @if (session('error'))
        <p>{{ session('error') }}</p>

    @endif

    @if (session('success'))
        <p>{{ session('success') }}</p>

    @endif

    <div class="container">

        <a href="{{ route('home') }}"><button type="button">Inventory</button></a>
        <a href="{{ route('transaction.history') }}"><button type="button">Transaction History</button></a>
        <h1>Point of Sale</h1>
        
        <div class="pos-container">

            <!-- PRODUCTS -->
            <div class="products">

                <h2>Products</h2>

                <div class="product-list">
                    @foreach ($items as $item)
                        <div class="product">
                            <h3>{{ $item->product_name }}</h3>
                            <p class="quantity"> {{ $item->quantity }}</p>
                            <p class="price">₱{{ $item->selling_price }}</p>
                            <form action="{{ route('cart.add', $item->id) }}" method="POST">
                                @csrf
                                <label for="quantity_{{ $item->id }}">Quantity:</label>
                                <input type="number" name="quantity" id="quantity_{{ $item->id }}" min="1" max="{{ $item->quantity }}" value="1"required>
                                <button type="submit">Add to Cart</button>
                            </form>
                        </div>
                    @endforeach
                </div>

            </div>


        
            <div class="cart">
                <h2>Cart</h2>

                @if (count($cart) > 0)

                    @foreach ($cart as $id => $item)

                        <div class="cart-item">

                            <h3>{{ $item['product_name'] }}</h3>
                            <p>Price:₱{{ $item['selling_price'] }}</p>
                            <p>Quantity:{{ $item['quantity'] }}</p>
                            <p>Subtotal:₱{{$item['subtotal']}}</p>

                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                <button type="submit">Remove</button>
                            </form>

                        </div>

                    @endforeach

                    <p class="total">Total:₱{{ $total }}</p>
                
                    <a href="{{ route('checkout') }}" class="checkout">Checkout</a>

                @else

                    <p>Your cart is empty.</p>

                @endif

            </div>

        </div>

    </div>

</body>

</html>