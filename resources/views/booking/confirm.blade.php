@extends('layouts.main')

@section('content')
<style>
    .container {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        margin-top: 50px;
    }
    h1, h3 {
        color: #6a11cb;
        text-align: center;
    }
</style>

<div class="container">
    <h1>Confirm Your Booking</h1>
    <h3>From: {{ $routeData['start_location'] }}</h3>
    <h3>To: {{ $routeData['end_location'] }}</h3>
    <h3>Customer Name: {{ $customerName }}</h3>
    <h3>Selected Seats: {{ implode(', ', $selectedSeats) }}</h3>
    <h3>Total Price: ₹{{ $totalPrice }}</h3>

    <form action="{{ route('booking.finalize') }}" method="POST">
        @csrf
        <input type="hidden" name="customer_name" value="{{ $customerName }}">
        <input type="hidden" name="selected_seats" value="{{ implode(',', $selectedSeats) }}">
        <button type="submit">Finalize Booking</button>
    </form>
</div>
@endsection
