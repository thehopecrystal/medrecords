<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'billing_period',
        'features', 'is_active', 'is_featured', 'badge', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features'    => 'array',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'price'       => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function isFree(): bool
    {
        return $this->price == 0 || $this->billing_period === 'free';
    }

    public function formattedPrice(): string
    {
        if ($this->billing_period === 'free' || $this->price == 0) {
            return 'Free';
        }
        return '₦' . number_format($this->price, 0) . ($this->billing_period === 'monthly' ? '/mo' : ($this->billing_period === 'yearly' ? '/yr' : ''));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
