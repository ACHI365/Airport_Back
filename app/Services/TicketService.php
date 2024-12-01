<?php

namespace App\Services;

use App\Models\Purchase;

class TicketService
{
    public static function createPurchase($quantity, $user_id, $schedule_id, $pricePerTicket): Purchase
    {
        $totalPrice = $pricePerTicket * $quantity;

        return Purchase::create([
            'user_id' => $user_id,
            'schedule_id' => $schedule_id,
            'total_price' => $totalPrice,
        ]);
    }

    public static function createTickets($user_id, $purchase_id, $schedule_id, $start_stop_id,
                                         $end_stop_id, $quantity, $pricePerTicket)
    {
        $tickets = [];
        for ($i = 0; $i < $quantity; $i++) {
            $tickets[] = [
                'user_id' => $user_id,
                'purchase_id' => $purchase_id,
                'schedule_id' => $schedule_id,
                'start_stop_id' => $start_stop_id,
                'end_stop_id' => $end_stop_id,
                'price' => $pricePerTicket,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $tickets;
    }
}
