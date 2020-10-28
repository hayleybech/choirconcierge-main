<?php

namespace App\Models;

use App\Mail\Welcome;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Mail;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use UnexpectedValueException;

// http://alexsears.com/article/adding-roles-to-laravel-users/
// https://medium.com/@ezp127/laravel-5-4-native-user-authentication-role-authorization-3dbae4049c8a

/**
 * Class User
 *
 * Columns
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Role[] $roles
 * @property GroupMember[] $memberships
 * @property Singer $singer
 *
 * @package App\Models
 */
class User extends Authenticatable implements HasMedia
{
    use Notifiable, InteractsWithMedia, BelongsToTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $with = [
        'media',
    ];

    public $notify_channels = ['database', 'mail'];
	
	/**
      * Get the roles a user has
      */
	public function roles(): BelongsToMany
    {
		return $this->belongsToMany(Role::class, 'users_roles');
	}

	 /**
      * Find out if User is an employee, based on if has any roles
      *
      * @return boolean
      */
    public function isEmployee(): bool
    {
       $roles = $this->roles->toArray();
       return ! empty($roles);
    }

    /**
     * Find out if user has a specific role
     *
     * $return boolean
     *
     * @param $check
     *
     * @return bool
     */
    public function hasRole($check): bool
    {
        return in_array($check, Arr::pluck($this->roles->toArray(), 'name'));
    }
	
	/**
     * Get key in array with corresponding value
     *
     * @return int
     */
    private function getIdInArray($array, $term)
    {
        foreach ($array as $key => $value) {
            if ($value === $term) {
                return $key;
            }
        }

        throw new UnexpectedValueException;
    }
	
	public function addRoles($ids): void
    {
		$this->roles()->attach($ids);
    }
	
	public function detachRole($id): void
    {
		
		$this->roles()->detach($id);
		
	}

	public function setPassword( string $password = null ): void {
        if( ! empty($password) ) {
            $this->password = Hash::make( $password );
        } else {
            $this->password = Str::random(10);
        }
    }
	
	/**
     * Add capabilities to user
     */
	 /*
    public function addCap($title)
    {
        $assigned_roles = [];

        $roles = array_fetch(Role::all()->toArray(), 'name');
 
        switch ($title) {
            case 'admin':
                $assigned_roles[] = $this->getIdInArray($roles, 'edit_user');
                $assigned_roles[] = $this->getIdInArray($roles, 'delete_user');
            case 'music_team':
                $assigned_roles[] = $this->getIdInArray($roles, 'create_voice_placement');
                $assigned_roles[] = $this->getIdInArray($roles, 'edit_voice_placement');
            case 'membership_team':
                $assigned_roles[] = $this->getIdInArray($roles, 'create_member_profile');
                $assigned_roles[] = $this->getIdInArray($roles, 'edit_member_profile');
			case 'accounts_team':
                $assigned_roles[] = $this->getIdInArray($roles, 'add_payment');
                break;
            default:
                throw new \Exception("The role entered does not exist");
        }

        $this->roles()->attach($assigned_roles);
    }*/

    /*
    * Get all groups this is a member of.
    */
    public function memberships(): MorphMany
    {
        return $this->morphMany(GroupMember::class, 'memberable');
    }

    public function singer(): HasOne
    {
        return $this->hasOne(Singer::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
            ])
            ->useFallbackUrl('https://avatars.dicebear.com/api/human/'.$this->id.'.svg?mood[]=happy')
            ->registerMediaConversions(function (Media $media){
                $this->addMediaConversion('thumb')
                    ->width(50)
                    ->height(50)
                    ->crop(Manipulations::CROP_CENTER, 50, 50);
                ;
                
                $this->addMediaConversion('profile')
                    ->width(150)
                    ->height(150)
                    ->crop(Manipulations::CROP_CENTER, 150, 150)
                ;
            });
    }

    public function getAvatarUrl(string $conversion): string
    {
        if( ! $this->hasMedia('avatar')){
            return $this->getFallbackMediaUrl('avatar');
        }

        if($this->getFirstMedia('avatar')->hasGeneratedConversion($conversion))
        {
            return $this->getFirstMediaUrl('avatar', $conversion);
        }
        return $this->getFirstMediaUrl('avatar');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('singer', static function(Builder $query){
            $query->whereHas('category', static function(Builder $query){
                $query->where('name', '=', 'Members');
            });
        });
    }

    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->whereHas('roles', static function(Builder $query) use ($role) {
            $query->where('name', '=', $role);
        });
    }

    /** @param string[] $roles */
    public function scopeWithRoles(Builder $query, array $roles): Builder
    {
        foreach($roles as $key => $role) {
            if($key === 0) {
                $query->whereHas('roles', static function(Builder $query) use ($role) {
                    $query->where('name', '=', $role);
                });
            }
            $query->orWhereHas('roles', static function(Builder $query) use ($role) {
                $query->where('name', '=', $role);
            });
        }
        return $query;
    }

    public function scopeWithoutRole(Builder $query, string $role): Builder
    {
        return $query->whereDoesntHave('roles', static function(Builder $query) use ($role) {
            $query->where('name', '=', $role);
        });
    }

    /** @param string[] $roles */
    public function scopeWithoutRoles(Builder $query, array $roles): Builder
    {
        foreach($roles as $key => $role) {
            $query->whereDoesntHave('roles', static function(Builder $query) use ($role) {
                $query->where('name', '=', $role);
            });
        }
        return $query;
    }

    public static function sendWelcomeEmail($user): void
    {
        // Generate a new reset password token
        $token = app('auth.password.broker')->createToken($user);

        // Send email
        Mail::send(new Welcome($user, $token));
    }

    public function hasAbility(string $ability): bool
    {
        foreach($this->roles as $role){
            if( in_array($ability, $role->abilities) ){
                return true;
            }
        }
        return false;
    }
}
