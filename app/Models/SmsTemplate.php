<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'message',
        'placeholders',
        'category',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parseMessage(array $data)
    {
        $message = $this->message;

        foreach ($data as $key => $value) {
            $message = str_replace('{{' . $key . '}}', $value, $message);
        }

        return $message;
    }

    public static function getBySlug($slug)
    {
        return self::where('slug', $slug)->where('is_active', true)->first();
    }
}
