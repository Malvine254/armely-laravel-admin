<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ChatAttachment extends Model
{
    protected $fillable = [
        'user_id',
        'chat_message_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'extracted_text',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function contents(): ?string
    {
        return Storage::disk($this->disk)->exists($this->path)
            ? Storage::disk($this->disk)->get($this->path)
            : null;
    }

    /**
     * Inline data URL for vision input. Attachments live on a private disk, so the model is
     * handed the bytes directly rather than a URL it would have to fetch.
     */
    public function toDataUrl(): ?string
    {
        $contents = $this->contents();

        return $contents === null
            ? null
            : 'data:' . $this->mime_type . ';base64,' . base64_encode($contents);
    }
}
