<?php

namespace app\model;

use support\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todos extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'todos';

    /**
     * 重定义主键，默认是id
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;
    protected $dateFormat = 'U';

    /**
     * 指示模型主键是否递增
     *
     * @var bool
     */
    public $incrementing = true;

    public static function boot()
    {
        parent::boot();
    }

    // 定义与 TodoCategories 的从属关系
    public function todoCategories(): BelongsTo
    {
        return $this->belongsTo(TodoCategories::class, 'category_id', 'id');
    }

    // 定义与 TodoOccurrences 的一对多关系
    public function todoOccurrences(): HasMany
    {
        return $this->hasMany(TodoOccurrences::class, 'todo_id', 'id');
    }
}
