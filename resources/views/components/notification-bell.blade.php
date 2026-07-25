<div>
    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        @if($unreadCount > 0)
        <span class="alert-count">{{ $unreadCount }}</span>
        @endif
        <i class='bx bx-bell'></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end">
        <a href="javascript:;">
            <div class="msg-header">
                <p class="msg-header-title">Notifications</p>
            </div>
        </a>
        <div class="header-notifications-list">
            @foreach($notifications as $notification)
            <a class="dropdown-item"
                href="{{ url( $notification->url ?? '' ) }}"
                onclick="markNotificationAsRead({{ $notification->id }})">
                <div class="d-flex align-items-center">
                    <div class="notify bg-light-danger text-danger"><i class="bx bx-cart-alt"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="msg-name">
                            @if($notification->type == 'stock_alert')
                            Stock Alert
                            @elseif($notification->type == 'sell_payment_date')
                            Sell Payment Date
                            @endif
                        </h6>
                        <p class="msg-info">
                            {{ $notification->message }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>