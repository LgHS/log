<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory, HasUuids;
    public array $authors = [];
    protected $fillable = ['title', 'author_id', 'submitter_id'];

    public function isAuthor(string $id) {
        return in_array($id, array_map(function ($user) { return $user->id; }, $this->authors));
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function postAuthors(): HasMany
    {
        return $this->hasMany(PostAuthor::class, 'post_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }
}
