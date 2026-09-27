<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Map Sales Columns</title>

</head>

<body>

    <h1>Map Your Sales Columns</h1>

    <p>
        Tell the system which columns contain the required information.
    </p>

    <form action="{{ route('sales.preview') }}" method="POST">

        @csrf
        <input type="hidden" name="file_path" value="{{ $filePath }}"
>
        <label for="product_column">Product Column:</label>

        <select name="product_column" id="product_column" required>

            @foreach ($headers as $header)

                <option value="{{ $header }}">
                    {{ $header }}
                </option>

            @endforeach

        </select>

        <br>
        <br>

        <label for="date_column">Date Column:</label>

        <select name="date_column" id="date_column" required>

            @foreach ($headers as $header)

                <option value="{{ $header }}">
                    {{ $header }}
                </option>

            @endforeach

        </select>

        <br>
        <br>

        <label for="quantity_column">Quantity Column:</label>

        <select name="quantity_column" id="quantity_column" required>

            @foreach ($headers as $header)

                <option value="{{ $header }}">
                    {{ $header }}
                </option>

            @endforeach

        </select>

        <br>
        <br>

        <button type="submit">
            Preview Data
        </button>

    </form>

</body>

</html>