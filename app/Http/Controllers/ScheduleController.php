<?php


namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\SeatAvailability;
use App\Queries\ScheduleQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{

    public function listSchedules(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'start_stop_id' => 'required|integer|exists:stops,id',
            'end_stop_id' => 'required|integer|exists:stops,id',
            'day' => 'required|integer|min:1|max:31',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $day = (int) $request->day;
        $month = (int) $request->month;

        $travelDate = now()->setDay($day)->setMonth($month)->toDateString();

        $schedules = ScheduleQuery::getAvailableSchedules(
            $request->start_stop_id,
            $request->end_stop_id,
            $travelDate
        );

        return response()->json($schedules);
    }

    public function create(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'stop_id' => 'required|exists:stops,id',
            'arrival_time' => 'required|date_format:H:i:s',
            'departure_time' => 'required|date_format:H:i:s',
            'date' => 'required|date',
            'totalSeats' => 'required|integer|min:1',
        ]);

        $schedule = Schedule::create($request->only([
            'bus_id', 'route_id', 'stop_id', 'arrival_time', 'departure_time', 'date'
        ]));

        $routeStops = DB::table('stops')
            ->where('route_id', $request->route_id)
            ->get();

        foreach ($routeStops as $stop) {
            SeatAvailability::create([
                'schedule_id' => $schedule->id,
                'stop_id' => $stop->id,
                'available_seats' => $request->totalSeats,
            ]);
        }

        return response()->json($schedule, 201);
    }

    public function getById($id)
    {
        $schedule = Schedule::with(['route.startStop', 'route.endStop', 'stop'])->findOrFail($id);

        return response()->json($schedule);
    }

    public function getAll()
    {
        $schedules = Schedule::with(['route.startStop', 'route.endStop', 'stop'])->get();

        return response()->json($schedules);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bus_id' => 'nullable|exists:buses,id',
            'route_id' => 'nullable|exists:routes,id',
            'stop_id' => 'nullable|exists:stops,id',
            'arrival_time' => 'nullable|date_format:H:i:s',
            'departure_time' => 'nullable|date_format:H:i:s',
            'date' => 'nullable|date',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update($request->only([
            'bus_id', 'route_id', 'stop_id', 'arrival_time', 'departure_time', 'date'
        ]));

        return response()->json($schedule);
    }

    public function delete($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully.']);
    }

}
