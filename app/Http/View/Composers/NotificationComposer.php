<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Ambil 5 notifikasi terbaru yang belum dibaca
            $unreadNotifications = $user->unreadNotifications()->latest()->take(5)->get();
            $unreadNotificationsCount = $user->unreadNotifications()->count();

            $view->with([
                'unreadNotifications' => $unreadNotifications,
                'unreadNotificationsCount' => $unreadNotificationsCount,
            ]);
        }
    }
}
