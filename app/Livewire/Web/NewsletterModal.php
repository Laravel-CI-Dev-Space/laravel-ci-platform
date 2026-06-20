<?php

declare(strict_types=1);

namespace App\Livewire\Web;

use App\Models\NewsletterSubscriber;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NewsletterModal extends Component
{
    public bool $open = false;

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:100')]
    public string $name = '';

    public bool $subscribed = false;

    public function subscribe(): void
    {
        $this->validate();

        $existing = NewsletterSubscriber::where('email', $this->email)->first();

        if ($existing) {
            if (! $existing->isActive()) {
                $existing->update(['unsubscribed_at' => null]);
            }
        } else {
            NewsletterSubscriber::create([
                'email' => $this->email,
                'name'  => $this->name ?: null,
            ]);
        }

        $this->subscribed = true;
    }

    public function dismiss(): void
    {
        $this->open = false;
    }

    public function render(): View
    {
        return view('livewire.web.newsletter-modal');
    }
}
