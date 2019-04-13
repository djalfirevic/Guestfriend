<?php

namespace App\Models;

use App\Contracts\SearchableModelInterface;
use App\Events\UserDeleted;
use App\Events\UserSaved;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Class User
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $api_token
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, SearchableModelInterface
{
    use Authenticatable, Authorizable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'api_token',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
        'email_verified_at',
        'remember_token',
        'deleted_at',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved'   => UserSaved::class,
        'deleted' => UserDeleted::class,
    ];

    /**
     * The attributes available for searching.
     *
     * @var array
     */
    protected $searchable = [
        'id',
        'name',
        'email',
        'created_at',
        'updated_at',
    ];

    /**
     * @return array
     */
    public function getSearchableAttributes(): array
    {
        return $this->searchable;
    }

    /**
     * @param array $data
     * @return User
     */
    public static function create(array $data = []): User
    {
        $model = new static($data);
        $model->api_token = self::generateApiToken();
        $model->save();

        return $model;
    }

    /**
     * @return string
     */
    public static function generateApiToken(): string
    {
        return Str::random(60);
    }

    /**
     * Get the tickets for the user.
     *
     * @return HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return bool|void|null
     * @throws Exception
     */
    public function delete()
    {
        $this->tickets()->delete();
        parent::delete();
    }
}
