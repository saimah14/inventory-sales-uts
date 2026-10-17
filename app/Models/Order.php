<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    protected $fillable = ['user_id', 'order_number', 'total', 'status', 'note', 'processed_by', 'processed_at', 'sale_id'];
    protected $casts = ['processed_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
    public function sale() { return $this->belongsTo(Sale::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
}
