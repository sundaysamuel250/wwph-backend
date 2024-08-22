<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $guarded= [];

    public function Host() {
        return $this->hasOne(User::class, "id", "user1");
    }

    
    public function ChatUser() {
        return $this->hasOne(User::class, "id", "user2");
    }

    
    public function messages() {
        return $this->hasMany(Message::class);
    }
}
