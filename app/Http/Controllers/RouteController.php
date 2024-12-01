<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'start_stop_id' => 'required|exists:stops,id',
            'end_stop_id' => 'required|exists:stops,id',
        ]);

        $route = Route::create($request->only(['start_stop_id', 'end_stop_id']));

        return response()->json($route, 201);
    }

    public function getById($id)
    {
        $route = Route::with(['startStop', 'endStop'])->findOrFail($id);

        return response()->json($route);
    }

    public function getAll()
    {
        $routes = Route::with(['startStop', 'endStop'])->get();

        return response()->json($routes);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'start_stop_id' => 'nullable|exists:stops,id',
            'end_stop_id' => 'nullable|exists:stops,id',
        ]);

        $route = Route::findOrFail($id);
        $route->update($request->only(['start_stop_id', 'end_stop_id']));

        return response()->json($route);
    }

    public function delete($id)
    {
        $route = Route::findOrFail($id);
        $route->delete();

        return response()->json(['message' => 'Route deleted successfully.']);
    }
}
