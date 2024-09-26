<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Seat;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $routes = Route::all();
        $locations = Location::all();
        return view('booking.index', compact('routes','locations'));
    }

    public function selectSeats(Request $request)
    {
        // return $request->input();
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'seat_type' => 'required',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');
        $seat_type = $request->input('seat_type');


        $route = Route::where('start_location', $from)
            ->where('end_location', $to)
            ->where('seat_type', $seat_type)
            ->first(); 


        $seats = Seat::where('seat_type', $seat_type)
            ->where('is_booked', false)
            ->get(); 

        if (!$route) {

            return redirect()->back()->with('error', 'Route not found.');
        }

        if ($seats->isEmpty()) {
            return redirect()->back()->with('error', 'No available seats found.');
        }

        $routeData = $route->toArray(); 
        // echo"<pre>";
        // print_r($routeData);die;
        return view('booking.select_seat', compact('routeData', 'seats'));
    }


    /* public function confirmBooking(Request $request)
    {
        return $request->input();   
        $seat = Seat::with('route')->find($request->seat_id);

        if (!$seat) {
            return redirect()->back()->withErrors(['msg' => 'Seat not found.']);
        }

        $seat->is_booked = true;
        $seat->save();

        Booking::create([
            'seat_id' => $seat->id,
            'customer_name' => $request->customer_name,
        ]);

        session([
            'route_name' => "{$seat->route->start_location} to {$seat->route->end_location}",
            'seat_number' => $seat->seat_number,
            'customer_name' => $request->customer_name,
        ]);

        return redirect()->route('booking.success');
    }*/


    public function success()
    {
        return view('booking.success');
    } 



public function confirmBooking(Request $request)
    {
        return $request->input(); 
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'selected_seats' => 'required|string', 
        ]);

        $customerName = $request->input('customer_name');
        $selectedSeats = explode(',', $request->input('selected_seats'));

        Seat::whereIn('seat_number', $selectedSeats)
            ->update(['is_booked' => 1]);

        $routeData = Route::find($request->input('route_id'));
        // echo "<pre>";
        // print_r($routeData);die;
        $totalPrice = count($selectedSeats) * $routeData->price;
        Booking::create([
            'route_id' => $routeData,
            'customer_name' => $customerName,
            'selected_seats' => implode(',', $selectedSeats),
            'total_price' => $totalPrice,
        ]);

        return view('booking.confirm', [
            'routeData' => $routeData,
            'customerName' => $customerName,
            'selectedSeats' => $selectedSeats,
            'totalPrice' => $totalPrice,
        ]);
    }


    
    public function finalizeBooking(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'selected_seats' => 'required|string',
        ]);

        $selectedSeats = explode(',', $request->input('selected_seats'));
        $customerName = $request->input('customer_name');

        // Logic to save booking
        foreach ($selectedSeats as $seatNumber) {
            Seat::where('seat_number', $seatNumber)->update(['is_booked' => true,]);
        }

        return redirect()->route('booking.success');
    }



    public function getLocation(Request $request)
    {
        if($request->ajax()){
            $from_location = $request->input('from');
            $locations = DB::table('locations')->where('name','!=', $from_location)->get();
            return json_encode($locations);
        };
    }
}

