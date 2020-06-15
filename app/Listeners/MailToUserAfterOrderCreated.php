<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MailToUserAfterOrderCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OrderCreated $event)
    {
        $order  = $event->order;
        //lay email
        $user =User::find($order->__get("user_id"));
        //nguoi dung lay don hang
    }

    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        //
    }
}
