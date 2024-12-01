<?php


namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Schedule;
use App\Queries\ScheduleQuery;
use Illuminate\Http\Request;

class BusController extends Controller
{

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $bus = Bus::create($request->only(['name', 'capacity']));

        return response()->json($bus, 201);
    }

    public function getById($id): \Illuminate\Http\JsonResponse
    {
        $bus = Bus::findOrFail($id);

        return response()->json($bus);
    }

    public function getAll(): \Illuminate\Http\JsonResponse
    {
        $buses = Bus::all();

        return response()->json($buses);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $bus = Bus::findOrFail($id);
        $bus->update($request->only(['name', 'capacity']));

        return response()->json($bus);
    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $bus = Bus::findOrFail($id);
        $bus->delete();

        return response()->json(['message' => 'Bus deleted successfully.']);
    }

}
