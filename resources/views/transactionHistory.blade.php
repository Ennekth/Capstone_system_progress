@extends('layouts.default')
@section('title') Transaction History @endsection

<body>

    

    <a href="{{ route('home') }}"><button type="button">Back to Home</button></a>
    <a href="{{ route('pos') }}" > <button>Point of Sale</button></a><h1>Transaction History</h1>
    <br>
    <br>

    @if ($transactions->count() > 0)

        <table border="1" cellpadding="10" cellspacing="0">

            <thead>

                <tr>
                    <th>Transaction ID</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($transactions as $transaction)

                    <tr>

                        <td>{{ $transaction->transaction_id }}</td>

                        <td>

                            @if ($transaction->transaction_type == 0)

                                Sale

                            @elseif ($transaction->transaction_type == 1)

                                Inventory Transfer

                            @endif

                        </td>

                        <td>

                            {{ $transaction->user ? $transaction->user->name : 'Unknown User' }}

                        </td>

                        <td>₱{{ $transaction->total_amount }}</td>

                        <td>{{ $transaction->created_at }}</td>

                        <td>

                            @if ($transaction->transaction_type == 0)

                                <a href="{{ route('receipt', $transaction->id) }}"><button type="button">View Receipt</button></a>

                            @else

                                Inventory Movement

                            @endif

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <p>No transactions found.</p>

    @endif

</body>

</html>