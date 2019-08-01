<?php

namespace App\Models\Contract;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Contract\Contracts
 *
 * @property int $id
 * @property int|null $author 作者
 * @property string|null $title 标题
 * @property string|null $content 内容
 * @property string|null $tags 标签
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract\Contracts whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contracts extends Model
{
    //
    protected $table = 'contracts';

    protected $fillable = [
        'title',
        'content',
        'author',
        'tags',
        'updated_at',
        'created_at',
    ];

    public function authorObj(){
        return $this->hasOne(User::class, 'id', 'author');
    }
}
