<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FailedJobs
 *
 * @property int $id
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property string $failed_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FailedJobs whereConnection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FailedJobs whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FailedJobs whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FailedJobs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FailedJobs wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FailedJobs whereQueue($value)
 * @mixin \Eloquent
 */
class FailedJobs extends Model
{
    protected $table = "failed_jobs";

    public static function list()
    {
        return FailedJobs::orderBy('id', 'desc')->paginate(20);
    }

    public static function queueList($queue)
    {
        return FailedJobs::where('queue', $queue)->orderBy('id', 'desc')->paginate(20);
    }

    public static function queues()
    {
        return FailedJobs::distinct()->get(['queue']);
    }
}
