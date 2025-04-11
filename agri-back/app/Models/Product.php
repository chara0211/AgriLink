<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Fillable properties allow mass assignment
    protected $fillable = [
        'name', 
        'price', 
        'stock', 
        'sales', 
        'revenue', 
        'status', 
        'farmer_id', // Adding farmer_id as fillable
        'image',   // Add 'image' to the fillable array
        'description',  // Add 'description' to the fillable array
    ];

    // Status enum values can be handled in the model like so (optional)
    const STATUS_AVAILABLE = 'Available';
    const STATUS_LOW_STOCK = 'Low Stock';
    const STATUS_OUT_OF_STOCK = 'Out of Stock';

    // Define the relationship to Farmer (One product belongs to one farmer)
    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    // Optionally, you can handle status like this to ensure it only takes valid values
    public function getStatusAttribute($value)
    {
        return ucfirst($value); // Capitalize the first letter when retrieving
    }

    // Automatically set the 'created_at' and 'updated_at' timestamps
    public $timestamps = true;
}
