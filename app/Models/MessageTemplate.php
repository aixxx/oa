<?php
namespace App\Models;

use App\Constant\CommonConstant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageTemplate
 *
 * @property int                   $template_id           主键
 * @property string                $template_key          模板键值
 * @property string                $template_name         名称
 * @property string                $template_type         类型
 * @property string                $template_sign         签名
 * @property string                $template_push_type    推送方式：email-邮件，wechat-企业微信，system-系统，sms-短信
 * @property string                $template_title        模板标题
 * @property string                $template_content      模板内容
 * @property string                $template_status       模板状态：active－可用，inactive－不可用
 * @property string                $template_memo         备注
 * @property int                   $template_created_user 创建用户
 * @property int                   $template_updated_user 更新用户
 * @property int                   $template_deleted      删除标记
 * @property int                   $template_deleted_user 删除用户
 * @property string                $template_deleted_at   删除时间
 * @property string                $template_created_at   创建时间
 * @property string                $template_updated_at   更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateCreatedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateMemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplatePushType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereTemplateUpdatedUser($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $createdUser
 * @property-read \App\Models\User $updatedUser
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate active($key)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageTemplate activeAll($key)
 * @method static MessageTemplate filterWhere($query, $column, $value = null, $operator = '=')
 * @method static MessageTemplate search(MessageTemplate $searchModel)
 */
class MessageTemplate extends Model
{
    public $timestamps = false;

    protected $table = "message_template";

    protected $primaryKey = 'template_id';

    protected $fillable = [
        'template_key',
        'template_name',
        'template_type',
        'template_sign',
        'template_push_type',
        'template_title',
        'template_content',
        'template_status',
        'template_memo',
        'template_created_user',
        'template_updated_user',
        'template_deleted',
        'template_deleted_user',
        'template_deleted_at',
        'template_created_at',
    ];

    protected $guarded = ['id', 'template_updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'template_created_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function updatedUser()
    {
        return $this->hasOne(User::class, 'id', 'template_updated_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deletedUser()
    {
        return $this->hasOne(User::class, 'id', 'template_deleted_user');
    }

    /**
     * @return bool|null
     */
    public function delete()
    {
        $this->template_deleted      = CommonConstant::FLAG_IS_DELETED;
        $this->template_deleted_user = auth()->id();
        $this->template_deleted_at   = Carbon::now()->toDateTimeString();

        return $this->save();
    }

    /**
     * @param \App\Models\MessageTemplate $query
     * @param string                      $key
     *
     * @return mixed
     */
    public function scopeActive($query, $key)
    {
        return $query->where('template_key', $key)
            ->where('template_status', CommonConstant::STATUS_ACTIVE)
            ->where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED);
    }

    /**
     * @param MessageTemplate $query
     * @param string          $key
     *
     * @return mixed
     */
    public function scopeActiveAll($query, $key)
    {
        return $query->active($key)->get();
    }

    /**
     * @param MessageTemplate $query
     * @param string          $column
     * @param null|mixed      $value
     * @param string          $operator
     *
     * @return mixed
     */
    public function scopeFilterWhere($query, $column, $value = null, $operator = '=')
    {
        if (!in_array($column, $this->fillable)) {
            return $query;
        }
        if (is_null($value) || mb_strlen(trim($value)) == 0) {
            return $query;
        }

        return $query->where($column, $operator, $value);
    }

    /**
     * @param MessageTemplate $query
     * @param MessageTemplate $searchModel
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopeSearch($query, MessageTemplate $searchModel)
    {
        return $query->where('template_deleted', CommonConstant::FLAG_IS_NOT_DELETED)
            ->filterWhere('template_key', $searchModel->template_key . '%', 'like')
            ->filterWhere('template_name', $searchModel->template_name . '%', 'like')
            ->filterWhere('template_type', $searchModel->template_type)
            ->filterWhere('template_push_type', $searchModel->template_push_type)
            ->filterWhere('template_status', $searchModel->template_status)
            ->paginate(20);
    }
}
