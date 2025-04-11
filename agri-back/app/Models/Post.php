<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id', // Farmer who creates the post
        'title', 
        'content',
        'image',  // Ajoutez 'image' ici
    ];
    

    // Relationship: A post belongs to a farmer (User)
    public function farmer()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
