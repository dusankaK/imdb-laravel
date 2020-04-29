<?php

namespace App\Listeners;

use App\Events\NewMovieAdded;
use App\Mail\NewMovieMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewMovieMail implements ShouldQueue
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
     * @param  object  $event
     * @return void
     */
    public function handle(NewMovieAdded $event)
    {
        Mail::to('duska@gmail.com')->send(new NewMovieMail($event->movie));
    }
}
