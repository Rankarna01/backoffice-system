<?php

namespace App\Filament\Pages;

use App\Domain\Notifications\Models\BroadcastNotification;
use App\Domain\Notifications\Models\NotificationLog;
use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationsPage extends Page
{
    protected string $view = 'filament.pages.notifications-page';

    protected static string|BackedEnum|null $navigationIcon = 'bx-bell';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?int $navigationSort = 80;

    protected static ?string $slug = 'notifications';

    // Filters
    public string $search = '';
    public string $statusFilter = 'all';
    public string $typeFilter = 'all';

    // Compose Modal / Form State
    public bool $isComposeOpen = false;
    public string $new_title = '';
    public string $new_body = '';
    public string $new_type = 'announcement';
    public string $new_target_audience = 'all';
    public string $new_action_url = '';
    public array $new_channels = ['in_app'];
    public bool $new_schedule_enabled = false;
    public ?string $new_scheduled_at = null;

    public function getTitle(): string
    {
        return 'Pusat Notifikasi & Siaran (Broadcast)';
    }

    public function mount(): void
    {
        //
    }

    public function openCompose(): void
    {
        $this->resetComposeForm();
        $this->isComposeOpen = true;
    }

    public function closeCompose(): void
    {
        $this->isComposeOpen = false;
    }

    public function resetComposeForm(): void
    {
        $this->new_title = '';
        $this->new_body = '';
        $this->new_type = 'announcement';
        $this->new_target_audience = 'all';
        $this->new_action_url = '';
        $this->new_channels = ['in_app'];
        $this->new_schedule_enabled = false;
        $this->new_scheduled_at = null;
    }

    public function saveBroadcast(bool $sendNow = false): void
    {
        $this->validate([
            'new_title' => 'required|string|max:255',
            'new_body' => 'required|string',
            'new_type' => 'required|string',
            'new_target_audience' => 'required|string',
        ]);

        $status = 'draft';
        $sentAt = null;
        $scheduledAt = null;

        if ($sendNow) {
            $status = 'sent';
            $sentAt = now();
        } elseif ($this->new_schedule_enabled && ! empty($this->new_scheduled_at)) {
            $status = 'scheduled';
            $scheduledAt = $this->new_scheduled_at;
        }

        $broadcast = BroadcastNotification::create([
            'title' => $this->new_title,
            'body' => $this->new_body,
            'type' => $this->new_type,
            'target_audience' => $this->new_target_audience,
            'action_url' => $this->new_action_url ?: null,
            'channels' => $this->new_channels ?: ['in_app'],
            'status' => $status,
            'scheduled_at' => $scheduledAt,
            'sent_at' => $sentAt,
            'created_by' => auth()->id(),
        ]);

        if ($sendNow) {
            $this->dispatchBroadcastToAudience($broadcast);
        }

        $this->isComposeOpen = false;
        $this->resetComposeForm();

        Notification::make()
            ->title($sendNow ? 'Notifikasi Berhasil Disiarkan' : 'Draf Notifikasi Disimpan')
            ->body($sendNow ? 'Pesan telah berhasil didistribusikan ke target audiens.' : 'Notifikasi siap dikirim sewaktu-waktu.')
            ->success()
            ->send();
    }

    public function sendNow(int $broadcastId): void
    {
        $broadcast = BroadcastNotification::find($broadcastId);
        if (! $broadcast) {
            return;
        }

        $broadcast->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->dispatchBroadcastToAudience($broadcast);

        Notification::make()
            ->title('Notifikasi Berhasil Dikirim')
            ->body("Siaran \"{$broadcast->title}\" telah didistribusikan.")
            ->success()
            ->send();
    }

    public function cancelBroadcast(int $broadcastId): void
    {
        $broadcast = BroadcastNotification::find($broadcastId);
        if ($broadcast) {
            $broadcast->update(['status' => 'cancelled']);

            Notification::make()
                ->title('Siaran Dibatalkan')
                ->body('Jadwal pengiriman notifikasi telah dibatalkan.')
                ->warning()
                ->send();
        }
    }

    public function deleteBroadcast(int $broadcastId): void
    {
        $broadcast = BroadcastNotification::find($broadcastId);
        if ($broadcast) {
            $broadcast->delete();

            Notification::make()
                ->title('Notifikasi Dihapus')
                ->body('Data riwayat siaran telah dihapus.')
                ->success()
                ->send();
        }
    }

    protected function dispatchBroadcastToAudience(BroadcastNotification $broadcast): void
    {
        // Query users based on target audience
        $query = User::query();

        if ($broadcast->target_audience === 'subscribers') {
            $query->whereHas('subscriptions', function ($q) {
                $q->where('status', 'active');
            });
        } elseif ($broadcast->target_audience === 'free_members') {
            $query->whereDoesntHave('subscriptions', function ($q) {
                $q->where('status', 'active');
            });
        }

        $recipients = $query->take(500)->get();

        $broadcast->update([
            'total_recipients' => $recipients->count(),
            'read_count' => 0,
        ]);

        // Bulk insert notification logs
        $logs = [];
        $channels = $broadcast->channels ?: ['in_app'];

        foreach ($recipients as $recipient) {
            foreach ($channels as $channel) {
                $logs[] = [
                    'broadcast_id' => $broadcast->id,
                    'user_id' => $recipient->id,
                    'channel' => $channel,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (! empty($logs)) {
            NotificationLog::insert($logs);
        }
    }

    public function getStatsProperty(): array
    {
        $totalBroadcasts = BroadcastNotification::count();
        $sentBroadcasts = BroadcastNotification::where('status', 'sent')->count();
        $totalRecipients = (int) BroadcastNotification::sum('total_recipients');
        $totalReads = (int) BroadcastNotification::sum('read_count');

        $avgReadRate = $totalRecipients > 0 ? round(($totalReads / $totalRecipients) * 100, 1) : 0.0;

        return [
            'total_broadcasts' => $totalBroadcasts,
            'sent_broadcasts' => $sentBroadcasts,
            'scheduled_count' => BroadcastNotification::where('status', 'scheduled')->count(),
            'total_recipients' => $totalRecipients,
            'avg_read_rate' => $avgReadRate,
        ];
    }

    public function getBroadcastsProperty()
    {
        $query = BroadcastNotification::with('creator')->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('body', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(10);
    }
}
