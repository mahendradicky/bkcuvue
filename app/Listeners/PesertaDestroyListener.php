<?php

namespace App\Listeners;

use App\Events\PesertaDestroy;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PesertaDestroyListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PesertaDestroy  $event
     * @return void
     */
    public function handle(PesertaDestroy $event)
    {
        //
    }
}
