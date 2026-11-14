<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Announcement extends Model
{
    protected $primaryKey = 'announcement_id';
=======
use App\Models\User;

class Announcement extends Model
{
    protected $table = 'announcements';

    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'title',
        'content',
        'target_roles',
        'created_by',
    ];
>>>>>>> 014121bbfae10bf11aad37e9d5abba9c3797084e

    protected $fillable = [
        'title', 'slug', 'category', 'content', 'image',
        'target_roles', 'is_public', 'published_at', 'created_by',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_public'    => 'boolean',
        'published_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'pengumuman'       => 'Pengumuman',
        'berita_sekolah'   => 'Berita Sekolah',
        'berita_kesiswaan' => 'Berita Kesiswaan',
        'agenda'           => 'Agenda',
    ];

    protected static function booted(): void
    {
<<<<<<< HEAD
        static::creating(function (Announcement $announcement) {
            if (empty($announcement->slug)) {
                $base = Str::slug($announcement->title) ?: 'pengumuman';
                $slug = $base;
                $i = 2;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }

                $announcement->slug = $slug;
            }
        });
=======
        return [
            'target_roles' => 'array',
        ];
>>>>>>> 014121bbfae10bf11aad37e9d5abba9c3797084e
    }

    public function getRouteKeyName(): string
    {
<<<<<<< HEAD
        return 'slug';
=======
        return $this->belongsTo(User::class, 'created_by', 'user_id');
>>>>>>> 014121bbfae10bf11aad37e9d5abba9c3797084e
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_public', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 120);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}