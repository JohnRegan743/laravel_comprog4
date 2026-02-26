<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function setCommentAttribute($value): void
    {
        $this->attributes['comment'] = static::cleanComment($value);
    }

    public static function cleanComment(string $comment): string
    {
        // Basic foul-word masking using regex
        $badWords = [
            'badword',
            'damn',
            'shit',
            'fuck',
        ];

        foreach ($badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $replacement = str_repeat('*', strlen($word));
            $comment = preg_replace($pattern, $replacement, $comment);
        }

        return $comment;
    }
}

