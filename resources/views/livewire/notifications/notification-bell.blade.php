<div class="notification-bell-wrapper position-relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">

    {{-- BELL BUTTON --}}
    <button type="button" class="notification-bell-btn" @click="open = ! open; if (open) $wire.loadNotifications()" aria-label="Notifications">
        <i class="ti ti-bell"></i>
        @if($unreadCount > 0)
            <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    {{-- DROPDOWN --}}
    <div class="notification-dropdown" x-show="open" x-cloak x-transition style="display: none;">
        <div class="notification-dropdown-header">
            <span>Notifications</span>
            @if($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="notification-mark-all">
                    Tout marquer lu
                </button>
            @endif
        </div>

        <div class="notification-dropdown-body">
            @forelse($notifications as $notification)
                @php $data = $notification->data; @endphp
                <a href="{{ $data['url'] ?? '#' }}"
                   wire:click="markAsRead('{{ $notification->id }}')"
                   class="notification-item {{ $notification->read_at === null ? 'is-unread' : '' }}">
                    <span class="notification-item-icon">
                        <i class="{{ $data['icon'] ?? 'ti ti-bell' }}"></i>
                    </span>
                    <span class="notification-item-content">
                        <span class="notification-item-message">{{ $data['message'] ?? '' }}</span>
                        <span class="notification-item-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                    @if($notification->read_at === null)
                        <span class="notification-unread-dot"></span>
                    @endif
                </a>
            @empty
                <div class="notification-empty">
                    <i class="ti ti-bell"></i>
                    <p>Aucune notification</p>
                </div>
            @endforelse
        </div>

        <div class="notification-dropdown-footer">
            <a href="{{ route('dashboard') }}">Voir toutes les notifications</a>
        </div>
    </div>

    <style>
        .notification-bell-wrapper { display: inline-flex; align-items: center; }
        .notification-bell-btn {
            position: relative;
            background: none;
            border: none;
            color: inherit;
            font-size: 1.15rem;
            padding: 0.5rem;
            cursor: pointer;
            line-height: 1;
        }
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #FF6600;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
        }
        .notification-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            width: 320px;
            max-width: 90vw;
            background: #fff;
            color: #1C1C2E;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 1050;
            overflow: hidden;
        }
        .notification-dropdown-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #eee;
            font-weight: 700;
        }
        .notification-mark-all {
            background: none;
            border: none;
            color: #FF6600;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
        }
        .notification-dropdown-body {
            max-height: 320px;
            overflow-y: auto;
        }
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: #1C1C2E;
            border-bottom: 1px solid #f5f5f5;
            position: relative;
        }
        .notification-item:hover { background: #f9f9f9; }
        .notification-item.is-unread { background: #fff7f0; }
        .notification-item-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FF6600;
            flex-shrink: 0;
        }
        .notification-item-content { display: flex; flex-direction: column; gap: 0.2rem; }
        .notification-item-message { font-size: 0.85rem; line-height: 1.4; }
        .notification-item-time { font-size: 0.72rem; color: #999; }
        .notification-unread-dot {
            position: absolute;
            top: 0.85rem;
            right: 0.75rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2ECC71;
        }
        .notification-empty {
            text-align: center;
            padding: 2rem 1rem;
            color: #999;
        }
        .notification-empty i { font-size: 1.5rem; margin-bottom: 0.5rem; display: block; }
        .notification-dropdown-footer {
            padding: 0.65rem 1rem;
            text-align: center;
            border-top: 1px solid #eee;
        }
        .notification-dropdown-footer a {
            font-size: 0.8rem;
            font-weight: 700;
            color: #FF6600;
            text-decoration: none;
        }
    </style>

</div>
