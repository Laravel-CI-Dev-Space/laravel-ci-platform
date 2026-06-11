<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class NotificationBell extends Component
{
    public Collection $notifications;

    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->notifications = $this->getNotifications();
        $this->unreadCount   = Auth::user()->unreadNotifications()->count();
    }

    /**
     * Recharge la liste des notifications et le compteur de non-lues.
     */
    public function loadNotifications(): void
    {
        $this->notifications = $this->getNotifications();
        $this->unreadCount   = Auth::user()->unreadNotifications()->count();
    }

    /**
     * Marque une notification comme lue.
     */
    public function markAsRead(string $id): void
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
            $this->unreadCount = max(0, $this->unreadCount - 1);
        }

        $this->notifications = $this->getNotifications();
    }

    /**
     * Marque toutes les notifications comme lues.
     */
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();

        $this->unreadCount   = 0;
        $this->notifications = $this->getNotifications();
    }

    /**
     * Récupère les 10 dernières notifications de l'utilisateur connecté.
     */
    private function getNotifications(): Collection
    {
        return Auth::user()->notifications()->latest()->limit(10)->get();
    }

    public function render(): View
    {
        return view('livewire.notifications.notification-bell', [
            'notifications' => $this->notifications,
            'unreadCount'   => $this->unreadCount,
        ]);
    }
}
