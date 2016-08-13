<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property string $shop_id
 * @property string $order_id
 * @property string $status
 * @property string $cost
 * @property string $currency
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $imported_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereShopId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCost($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order whereImportedAt($value)
 */
class Order extends Model
{
    protected $fillable = ['shop_id', 'order_id', 'status', 'cost', 'currency', 'imported_at', 'created_at'];

	//
}
