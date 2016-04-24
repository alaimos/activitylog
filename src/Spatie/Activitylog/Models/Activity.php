<?php

namespace Spatie\Activitylog\Models;

use Eloquent;
use Config;
use Exception;

/**
 * \Spatie\Activitylog\Models\Activity
 *
 * @property integer        $id
 * @property integer        $user_id
 * @property-read object    $user
 * @property string         $text
 * @property string         $ip_address
 * @property string         $level
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Spatie\Activitylog\Models\Activity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Spatie\Activitylog\Models\Activity whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Spatie\Activitylog\Models\Activity whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\Spatie\Activitylog\Models\Activity whereIpAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\Spatie\Activitylog\Models\Activity whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\Spatie\Activitylog\Models\Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Spatie\Activitylog\Models\Activity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Activity extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_log';

    /**
     * Get the user that the activity belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @throws Exception
     */
    public function user()
    {
        return $this->belongsTo($this->getAuthModelName(), 'user_id');
    }

    public function getAuthModelName()
    {
        if (config('activitylog.userModel')) {
            return config('activitylog.userModel');
        }

        //laravel 5.0 - 5.1
        if (!is_null(config('auth.model'))) {
            return config('auth.model');
        }

        //laravel 5.2
        if (!is_null(config('auth.providers.users.model'))) {
            return config('auth.providers.users.model');
        }

        throw new Exception('could not determine the model name for users');
    }

    protected $guarded = ['id'];
}
