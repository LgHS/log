<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostAuthor extends Model
{
    public $timestamps = false;

    public $table = 'post_author';

    protected $fillable = ['user_id', 'post_id'];
    public array|null $authors = null;

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

}
