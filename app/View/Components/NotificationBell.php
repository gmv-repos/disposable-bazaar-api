<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Notification;

class NotificationBell extends Component
{
    public $unreadCount;
    public $notifications;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->unreadCount = Notification::where('is_read', 0)->count();
        $this->notifications = Notification::where('is_read', 0)->orderByDesc('id')->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.notification-bell');
    }
}
