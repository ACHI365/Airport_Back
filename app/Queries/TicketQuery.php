<?php

namespace App\Queries;

use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Support\Facades\DB;

class TicketQuery
{
    private static function getSchedule($schedule_id)
    {
        return DB::table('schedules')
            ->where('schedules.id', $schedule_id)
            ->first();
    }

    private static function getAvailableSeats($schedule_id, $stop_id)
    {
        return DB::table('seat_availabilities')
            ->where('schedule_id', $schedule_id)
            ->where('stop_id', $stop_id)
            ->min('available_seats');
    }

    private static function getAffectedSchedules($route_id, $start_stop_id, $end_stop_id)
    {
        return DB::table('schedules')
            ->where('route_id', $route_id)
            ->whereBetween('stop_id', [$start_stop_id, $end_stop_id])
            ->where('stop_id', '!=', $end_stop_id)
            ->orderBy('arrival_time')
            ->pluck('id', 'stop_id');
    }

    private static function updateAvailableSeats($scheduleId, $stopId, $quantity): void
    {
        DB::table('seat_availabilities')
            ->where('schedule_id', $scheduleId)
            ->where('stop_id', $stopId)
            ->decrement('available_seats', $quantity);
    }

    public static function buyTicket($request, $user): \Illuminate\Http\JsonResponse
    {
        return DB::transaction(function () use ($request, $user) {
            // Step 1: Get Schedules
            $schedule = self::getSchedule($request->schedule_id);

            if (!$schedule) {
                return response()->json(['message' => 'Invalid schedule for the selected stops.'], 400);
            }

            if ($schedule->stop_id == $request->end_stop_id) {
                return response()->json(['message' => 'Invalid stops received'], 400);
            }

            // Step 2: Check available seats
            $availableSeats = self::getAvailableSeats($schedule->id, $schedule->stop_id);

            if ($availableSeats < $request->quantity) {
                return response()->json(['message' => 'Not enough seats available.'], 400);
            }

            // Step 3: Determine affected schedules for the route
            $affectedSchedules = self::getAffectedSchedules($schedule->route_id, $schedule->stop_id, $request->end_stop_id);

            // Step 4: Decrement seat availability for the relevant stops
            foreach ($affectedSchedules as $stopId => $scheduleId) {
                self::updateAvailableSeats($scheduleId, $stopId, $request->quantity);
            }

            // Step 5: Create the purchase
            $pricePerTicket = 50.00;
            $purchase = TicketService::createPurchase($request->quantity, $user->id, $schedule->id, $pricePerTicket);

            // Step 6: Create tickets for the purchase
            $tickets = TicketService::createTickets($user->id, $purchase->id, $schedule->id,
                $schedule->stop_id, $request->end_stop_id, $request->quantity, $pricePerTicket);
            Ticket::insert($tickets); // Bulk insert tickets

            return response()->json([
                'message' => 'Purchase successful',
                'purchase_id' => $purchase->id,
                'total_price' => $purchase->total_price,
            ], 201);
        });
    }
}
