<?php


namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Ticket;
use App\Queries\TicketQuery;
use App\Services\TicketService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function buyTicket(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'end_stop_id' => 'required|exists:stops,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        return TicketQuery::buyTicket($request, $user);
    }
}
