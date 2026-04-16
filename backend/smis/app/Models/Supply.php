<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_supply';

    // Define the custom primary key
    protected $primary_key = 'stock_num';

    // Tell Laravel that stock_num is not an auto-incrementing integer
    public $incrementing = false;
    protected $key_type = 'string';

    protected $fillable = [
        'stock_num',
        'item_desc',
        'quantity',
        'status',
        'remarks',
        'category_id',
        'unit_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
