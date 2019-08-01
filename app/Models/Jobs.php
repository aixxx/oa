<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Jobs
 *
 * @property int $id
 * @property string $queue
 * @property string $payload
 * @property int $attempts
 * @property int|null $reserved_at
 * @property int $available_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Jobs whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Jobs whereAvailableAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Jobs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Jobs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Jobs wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Jobs whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Jobs whereReservedAt($value)
 * @mixin \Eloquent
 */
class Jobs extends Model
{
    protected $table = "jobs";

    public static function list()
    {
        return self::orderBy('id', 'desc')->paginate(20);
    }

    public static function queueList($queue)
    {
        return self::where('queue', $queue)->orderBy('id', 'desc')->paginate(20);
    }

    public static function queues()
    {
        return self::distinct()->get(['queue']);
    }
}
