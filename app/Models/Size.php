<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $primaryKey = 'size_id';
    public $timestamps = false;

    protected $casts = [
        'value' => 'decimal:1',
    ];

    public function stockItems()
    {
        return $this->hasMany(StockItem::class, 'size_id', 'size_id');
    }
}
