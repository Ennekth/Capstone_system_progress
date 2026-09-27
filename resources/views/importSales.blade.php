@extends('layouts.default')
@section('title') Import Sales @endsection

<body>
@if (session('success'))

        <p>
            {{ session('success') }}
        </p>

@endif

@if (session('error'))

    <p>
        {{ session('error') }}
    </p>

@endif
    <h1>Import Historical Sales Records</h1>

    <p>
        Upload a CSV file containing your historical sales data.
    </p>

    @if (session('error'))

        <p>
            {{ session('error') }}
        </p>

    @endif

    <form action="{{ route('sales.upload') }}" method="POST" enctype="multipart/form-data">

        @csrf

        <label for="sales_file">Choose Sales File:</label>

        <input
            type="file"
            name="sales_file"
            id="sales_file"
            accept=".csv"
            required
        >

        <br>
        <br>

        <button type="submit">
            Upload and Continue
        </button>

    </form>

    <br>

    <a href="{{ route('home') }}">
        Back to Home
    </a>

</body>

</html>