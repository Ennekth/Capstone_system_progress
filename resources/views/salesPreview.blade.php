<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sales Preview</title>

</head>

<body>

    <h1>Sales Import Preview</h1>

    <table border="1">

    <thead>

        <tr>

            <th>Product</th>

            <th>Date</th>

            <th>Quantity</th>

            <th>Status</th>

        </tr>

    </thead>

    <tbody>

        @foreach ($records as $record)

            <tr>

                <td>
                    {{ $record['product_name'] }}
                </td>

                <td>
                    {{ $record['date'] }}
                </td>

                <td>
                    {{ $record['quantity'] }}
                </td>

                <td>

                    @if ($record['status'] === 'Valid')

                        Valid

                    @else

                        Invalid:
                        {{ $record['message'] }}

                    @endif

                </td>

            </tr>

        @endforeach

    </tbody>

</table>

    <br>

    <button type="button">
        Confirm Import
    </button>

    <br>
    <br>

    @php

    $hasInvalidRecords = false;

    foreach ($records as $record) {

        if ($record['status'] !== 'Valid') {

            $hasInvalidRecords = true;

            break;

        }

    }

@endphp


@if ($hasInvalidRecords)

    <p>
        Some records are invalid. Please correct the file
        before importing.
    </p>

@else

    <form action="{{ route('sales.confirm') }}" method="POST">

        @csrf

        <input
            type="hidden"
            name="file_path"
            value="{{ $data['file_path'] }}"
        >

        <input
            type="hidden"
            name="product_column"
            value="{{ $data['product_column'] }}"
        >

        <input
            type="hidden"
            name="date_column"
            value="{{ $data['date_column'] }}"
        >

        <input
            type="hidden"
            name="quantity_column"
            value="{{ $data['quantity_column'] }}"
        >

        <button type="submit">
            Confirm Import
        </button>

    </form>

@endif

</body>

</html>