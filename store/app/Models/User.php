<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Public URL for this user's avatar, served through the profile-picture route. Built from
     * the current request host so it never becomes a cross-origin image the page CSP blocks.
     */
    public function profilePictureUrl(): ?string
    {
        $path = $this->normalizedProfilePicturePath();
        if ($path === null) {
            return null;
        }

        $absolutePath = storage_path('app/public/' . $path);
        if (!is_file($absolutePath)) {
            return null;
        }

        $base = rtrim((string) config('app.asset_url', ''), '/');
        if ($base === '') {
            try {
                $base = request() ? rtrim((string) request()->getSchemeAndHttpHost(), '/') : '';
            } catch (\Throwable $e) {
                $base = '';
            }
        }
        if ($base === '') {
            $base = rtrim((string) config('app.frontend_url'), '/');
        }

        $encoded = implode('/', array_map('rawurlencode', explode('/', $path)));

        return $base . '/api/v1/profile-pictures/' . $encoded . '?v=' . (string) @filemtime($absolutePath);
    }

    private function normalizedProfilePicturePath(): ?string
    {
        $value = str_replace('\\', '/', trim((string) $this->profile_picture));
        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $value = (string) parse_url($value, PHP_URL_PATH) ?: $value;
        }

        $value = ltrim($value, '/');
        if (($position = strpos($value, '/storage/')) !== false) {
            $value = substr($value, $position + strlen('/storage/'));
        }
        foreach (['storage/', 'public/'] as $prefix) {
            if (str_starts_with($value, $prefix)) {
                $value = substr($value, strlen($prefix));
            }
        }

        $value = ltrim($value, '/');

        return $value === '' || str_contains($value, '..') ? null : $value;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'company_id',
        'role',
        'status',
        'profile_picture',
        'stripe_customer_id',
        'payment_methods_consent',
        'special_pricing_percent',
        'assigned_shipping_amount',
        'force_password_change',
        'temp_password_expires_at',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'payment_methods_consent' => 'boolean',
            'special_pricing_percent' => 'decimal:2',
            'assigned_shipping_amount' => 'decimal:2',
            'force_password_change' => 'boolean',
            'temp_password_expires_at' => 'datetime',
            'permissions' => 'array',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function emailPreference()
    {
        return $this->hasOne(EmailPreference::class);
    }
}
