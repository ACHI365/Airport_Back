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
            'route_id' => 'required|integer|exists:routes,id',
            'date' => 'required|date',
        ]);
        // dd($request->all());


        $schedules = Schedule::query()
            ->where('route_id', $request->route_id)
            ->where('date', $request->date)
            ->get();

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
            'bus_id',
            'route_id',
            'stop_id',
            'arrival_time',
            'departure_time',
            'date'
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
            'bus_id',
            'route_id',
            'stop_id',
            'arrival_time',
            'departure_time',
            'date'
        ]));

        return response()->json($schedule);
    }

    public function delete($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully.']);
    }

    public function createBulk(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'start_date' => 'required|date',
            'consecutive_days' => 'required|integer|min:1|max:365',
            'arrival_time' => 'required|date_format:H:i:s',
            'departure_time' => 'required|date_format:H:i:s',
        ]);

        $schedules = [];
        $startDate = \Carbon\Carbon::parse($request->start_date);

        // find the bus
        $bus = DB::table('buses')->where('id', $request->bus_id)->first();
        $bus_seats = $bus->capacity;

        for ($i = 0; $i < $request->consecutive_days; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            
            $schedule = Schedule::create([
                'bus_id' => $request->bus_id,
                'route_id' => $request->route_id,
                'date' => $currentDate->format('Y-m-d'),
                'arrival_time' => $request->arrival_time,
                'departure_time' => $request->departure_time,
                'available_seats' => $bus_seats,
            ]);

            $schedules[] = $schedule;
        }

        return response()->json($schedules, 201);
    }

}
