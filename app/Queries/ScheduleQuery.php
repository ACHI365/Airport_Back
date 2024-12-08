<?php

namespace App\Queries;

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class ScheduleQuery
{
    // COULD REFACTOR
    private static function getValidRoutes($startStopId, $endStopId): \Illuminate\Support\Collection
    {
        return DB::table('schedules as start')
            ->join('schedules as end_schedule', 'start.route_id', '=', 'end_schedule.route_id')  // Renamed 'end' to 'end_schedule'
            ->where('start.stop_id', $startStopId)
            ->where('end_schedule.stop_id', $endStopId)
            ->where(function ($query) {
                $query->where('start.date', '<', DB::raw('end_schedule.date'))
                    ->orWhere(function ($query) {
                        $query->where('start.date', '=', DB::raw('end_schedule.date'))
                            ->where('start.departure_time', '<', DB::raw('end_schedule.arrival_time'));
                    });
            })
            ->distinct()
            ->pluck('start.route_id');
    }

    private static function getSchedules($startStopId, $travelDate, $endStopId): \Illuminate\Support\Collection
    {
        // dd($startStopId, $travelDate); 
        $res =   
     Schedule::query()
        ->whereHas('route', function ($query) use ($startStopId, $endStopId) {
            $query->where('start_stop_id', $startStopId);
            // ->where('end_stop_id', $endStopId);
        })
        // ->where('date', $travelDate)
            ->get();

        dd($res, $startStopId, $travelDate, $endStopId );
        return $res;    
        // return DB::table('schedules as start')
        //     ->join('routes', 'start.route_id', '=', 'routes.id')
        //     ->join('stops as start_stop', 'routes.start_stop_id', '=', 'start_stop.id')
        //     ->join('stops as end_stop', 'routes.end_stop_id', '=', 'end_stop.id')
        //     ->where('start.stop_id', $startStopId) // Filter for starting stop
        //     ->whereIn('start.route_id', $validRoutes) // Filter for valid routes
        //     ->where('start.date', $travelDate) // Date restriction for start schedules
        //     ->select(
        //         'start.id as schedule_id',
        //         'start.departure_time',
        //         'start.date as departure_date',
        //         'routes.id as route_id',
        //         'routes.start_stop_id',
        //         'routes.end_stop_id',
        //         DB::raw("CONCAT(start_stop.stop_name, ' - ', end_stop.stop_name) as route_name")
        //     )
        //     ->orderBy('start.departure_time')
        //     ->get();
    }

    private static function getAvailableSeats($startStopId, $scheduleIds): \Illuminate\Support\Collection
    {
        return DB::table('seat_availabilities')
            ->whereIn('schedule_id', $scheduleIds)
            ->where('stop_id', $startStopId)
            ->groupBy('schedule_id')
            ->select('schedule_id', DB::raw('MIN(available_seats) as available_seats')) // Aggregate by minimum available seats
            ->pluck('available_seats', 'schedule_id'); // Map schedule_id => available_seats
    }

    public static function getAvailableSchedules($startStopId, $endStopId, $travelDate): \Illuminate\Support\Collection
    {
        $travelDate = \Carbon\Carbon::parse($travelDate)->toDateString();

        $validRoutes = self::getValidRoutes($startStopId, $endStopId);

        $schedules = self::getSchedules($startStopId, $travelDate, $endStopId);

        // Step 3: Get all schedule IDs
        $scheduleIds = $schedules->pluck('schedule_id');

        // Step 4: Fetch available seats for each schedule
        $seatAvailability = self::getAvailableSeats($startStopId, $scheduleIds);

        // Step 5: Map results with available seats
        return $schedules->map(function ($schedule) use ($seatAvailability) {
            return [
                'schedule_id' => $schedule->schedule_id,
                'departure_time' => "{$schedule->departure_date} {$schedule->departure_time}",
                'route_name' => $schedule->route_name,
                'available_seats' => $seatAvailability[$schedule->schedule_id] ?? 0, // Default to 0 if not found
            ];
        });
    }
}
