<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsDetails extends Model
{
    use HasFactory;
    // Define the table if it's not following the default Laravel naming convention
    protected $table = 'sms_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id', 'phone_number', 'recipient_name', 'status', 'message_type', 'message'];

    /**
     * Status constants.
     */
    const STATUS_PENDING = 1;
    const STATUS_FORWARDED = 2;
    const STATUS_ERROR = 3;

    /**
     * Get the order associated with the SMS.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get a human-readable status for the SMS.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'Pending';
            case self::STATUS_FORWARDED:
                return 'Forwarded';
            case self::STATUS_ERROR:
                return 'Error';
            default:
                return 'Unknown';
        }
    }

    /**
     * Check if the SMS is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the SMS is forwarded.
     *
     * @return bool
     */
    public function isForwarded()
    {
        return $this->status === self::STATUS_FORWARDED;
    }

    /**
     * Check if the SMS has encountered an error.
     *
     * @return bool
     */
    public function isError()
    {
        return $this->status === self::STATUS_ERROR;
    }
}
