<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Demand Forecast</title>

</head>

<body>

    <h1>Demand Forecast</h1>

    <form action="{{ route('forecast.calculate') }}" method="GET">

        <label for="product_id">
            Product:
        </label>

        <select
            name="product_id"
            id="product_id"
            required
        >

            <option value="">
                Select Product
            </option>

            @foreach ($products as $product)

                <option
                    value="{{ $product->id }}"
                >
                    {{ $product->product_name }}
                </option>

            @endforeach

        </select>

        <br>
        <br>

        <label for="sma_period">
            SMA Timeframe:
        </label>

        <input
            type="number"
            name="sma_period"
            id="sma_period"
            min="1"
            value="3"
            required
        >

        <select
            name="sma_unit"
            id="sma_unit"
            required
        >

            <option value="month">
                Months
            </option>

            <option value="week">
                Weeks
            </option>

            <option value="day">
                Days
            </option>

        </select>

        <br>
        <br>

        <button type="submit">
            Generate Forecast
        </button>

    </form>

    <br>

    <a href="{{ route('home') }}">
        <button type="button">
            Back to Home
        </button>
    </a>

</body>

</html>