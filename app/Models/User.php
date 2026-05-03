<?php

namespace App\Models;

use App\Core\Website\Models\Role;
use App\Core\Website\Models\Website;
use App\Core\Website\Models\WebsiteUser;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function websiteMemberships(): HasMany
    {
        return $this->hasMany(WebsiteUser::class);
    }

    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class, 'website_users')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(
            Role::class,
            WebsiteUser::class,
            'user_id',
            'id',
            'id',
            'role_id',
        );
    }

    public function hasRole(string|array $roles, ?Website $website = null): bool
    {
        $roles = array_map('strtolower', (array) $roles);

        $query = $this->websiteMemberships()->with('role');

        if ($website) {
            $query->where('website_id', $website->getKey());
        }

        return $query->get()->contains(function (WebsiteUser $membership) use ($roles) {
            return $membership->role && in_array(Str::lower($membership->role->slug), $roles, true);
        });
    }
}
