<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'telegram', 'telegram_chat_id', 'viber', 'whatsapp', 'unp', 'avatar', 'has_detailed_stats'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_detailed_stats' => 'boolean',
        ];
    }

    /**
     * Get owner's Telegram Chat ID, checking user model field and telegram_users table
     */
    public function getTelegramChatId(): ?string
    {
        if (!empty($this->telegram_chat_id)) {
            return (string)$this->telegram_chat_id;
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('telegram_users')) {
                $tgUser = TelegramUser::where('user_id', $this->id)
                    ->where('notifications_enabled', true)
                    ->first();
                if ($tgUser && !empty($tgUser->chat_id)) {
                    return (string)$tgUser->chat_id;
                }

                if (!empty($this->telegram)) {
                    $cleanUsername = ltrim(trim($this->telegram), '@');
                    $tgUser = TelegramUser::where('username', $cleanUsername)
                        ->where('notifications_enabled', true)
                        ->first();
                    if ($tgUser && !empty($tgUser->chat_id)) {
                        return (string)$tgUser->chat_id;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Error resolving telegram_chat_id for user ' . $this->id . ': ' . $e->getMessage());
        }

        return null;
    }
}
