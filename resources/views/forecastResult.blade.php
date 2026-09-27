<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forecast Result</title>

</head>

<body>

    <h1>Demand Forecast</h1>

    <h2>
        {{ $product->product_name }}
    </h2>

    <p>
        SMA Timeframe:
        {{ $smaPeriod }}
        {{ $smaUnit }}(s)
    </p>

    <h2>
        Historical Demand
    </h2>

    <table border="1">

        <thead>

            <tr>

                <th>Period</th>

                <th>Total Demand</th>

            </tr>

        </thead>

        <tbody>

            @foreach ($groupedDemand as $period => $quantity)

                <tr>

                    <td>
                        {{ $period }}
                    </td>

                    <td>
                        {{ $quantity }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

    <br>

    <h2>
        SMA Forecast
    </h2>

    <p>
        Total Demand:
        {{ $totalDemand ?? array_sum($groupedDemand) }}
    </p>

    <p>
        Number of Periods:
        {{ count($groupedDemand) }}
    </p>

    <p>
        Forecasted Average Demand:
        {{ number_format($sma, 2) }}
        units per {{ $smaUnit }}
    </p>

    <br>

    <a href="{{ route('forecast') }}">

        <button type="button">
            Back
        </button>

    </a>

</body>

</html>