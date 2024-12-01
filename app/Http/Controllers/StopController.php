<?php namespace App\Http\Controllers;

use App\Models\Stop;
use Illuminate\Http\Request;

class StopController extends Controller
{
    public function create(Request $request)
    {
        $request->validate(['stop_name' => 'required|string|max:255|unique:stops,stop_name', 'latitude' => 'required|numeric', 'longitude' => 'required|numeric',]);
        $stop = Stop::create($request->only(['stop_name', 'latitude', 'longitude']));
        return response()->json($stop, 201);
    }

    public function getById($id)
    {
        $stop = Stop::findOrFail($id);
        return response()->json($stop);
    }

    public function getAll()
    {
        $stops = Stop::all();
        return response()->json($stops);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['stop_name' => 'nullable|string|max:255|unique:stops,stop_name', 'latitude' => 'nullable|numeric', 'longitude' => 'nullable|numeric',]);
        $stop = Stop::findOrFail($id);
        $stop->update($request->only(['stop_name', 'latitude', 'longitude']));
        return response()->json($stop);
    }

    public function delete($id)
    {
        $stop = Stop::findOrFail($id);
        $stop->delete();
        return response()->json(['message' => 'Stop deleted successfully.']);
    }
}
