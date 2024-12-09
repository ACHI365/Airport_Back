<?php

namespace App\Queries;

use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Support\Facades\DB;
use Log;

class TicketQuery
{
    private static function getSchedule($schedule_id)
    {
        return Schedule::find($schedule_id);
    }

    private static function getAvailableSeats($schedule_id, $stop_id)
    {
        // get available seat attribute from the schedule
        return Schedule::find($schedule_id)->available_seats;
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

    private static function updateAvailableSeats($scheduleId, $quantity): void
    {
        $schedule = Schedule::find($scheduleId);
        $schedule->available_seats -= $quantity;
        $schedule->save();
    }

    // count how many tickets are already bought by this user on this schedule
    private static function countTickets($user_id, $schedule_id): int
    {
        return Ticket::where('user_id', $user_id)
            ->where('schedule_id', $schedule_id)
            ->count();
    }

    public static function buyTicket($request, $user): \Illuminate\Http\JsonResponse
    {
        return DB::transaction(function () use ($request, $user) {
            // Step 1: Get Schedules
            // find schedule based on route id and date
            $schedule = Schedule::find($request->schedule_id);

            if (!$schedule) {
                return response()->json(['message' => 'Invalid schedule for the selected stops.'], 400);
            }
            $route = $schedule->route;
            if ($route->start_stop_id == $request->end_stop_id) {
                return response()->json(['message' => 'Invalid stops received'], 400);
            }

            // Step 2: Check available seats
            $availableSeats = self::getAvailableSeats($schedule->id, $schedule->stop_id);

            if ($availableSeats < $request->quantity) {
                return response()->json(['message' => 'Not enough seats available.'], 400);
            }

            // update available seats
            self::updateAvailableSeats($schedule->id, $request->quantity);

            // Step 3: Check if user already bought tickets for this schedule
            $ticketCount = self::countTickets($user->id, $schedule->id);

            if ($ticketCount + $request->quantity > 3) {
                return response()->json(['message' => 'Can not buy more than 3'], 400);
            }
            
            // Step 5: Create the purchase  
            $pricePerTicket = $schedule->route->price;

            $purchase = TicketService::createPurchase($request->quantity, $user->id, $schedule->id, $pricePerTicket);

            // Step 6: Create tickets for the purchase
            $tickets = TicketService::createTickets(
                $user->id,
                $purchase->id,
                $schedule->id,
                $schedule->stop_id,
                $request->end_stop_id,
                $request->quantity,
                $pricePerTicket
            );
            Ticket::insert($tickets); // Bulk insert tickets

            return response()->json([
                'message' => 'Purchase successful',
                'purchase_id' => $purchase->id,
                'total_price' => $purchase->total_price,
            ], 201);
        });
    }
}
