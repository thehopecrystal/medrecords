<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'title', 'body', 'data', 'read_at'];

    protected function casts(): array
    {
        return ['data' => 'array', 'read_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }

    public function isUnread(): bool { return $this->read_at === null; }

    public static function send(int $userId, string $type, string $title, string $body, array $data = []): void
    {
        static::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);
    }
}
