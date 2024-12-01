<?php


namespace App\Services;


use Illuminate\Database\Eloquent\Collection;


class UserService
{
    public static function purchaseDTO(Collection $purchases): Collection|\Illuminate\Support\Collection
    {
        return $purchases->map(function ($purchase) {
            return [
                'purchase_id' => $purchase->id,
                'schedule_id' => $purchase->schedule_id,
                'route_name' => "{$purchase->schedule->route->startStop->stop_name} - {$purchase->schedule->route->endStop->stop_name}",
                'total_price' => $purchase->total_price,
                'tickets' => $purchase->tickets->map(function ($ticket) {
                    return [
                        'ticket_id' => $ticket->id,
                        'start_stop_id' => $ticket->start_stop_id,
                        'end_stop_id' => $ticket->end_stop_id,
                        'price' => $ticket->price,
                    ];
                }),
            ];
        });
    }
}
