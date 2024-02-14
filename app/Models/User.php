<?php

namespace App\Models;

use App\Mail\Welcome;
use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

// http://alexsears.com/article/adding-roles-to-laravel-users/
// https://medium.com/@ezp127/laravel-5-4-native-user-authentication-role-authorization-3dbae4049c8a

/**
 * Class User
 *
 * Columns
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $pronouns
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property Carbon $dob
 * @property string $phone
 * @property string $ice_name
 * @property string $ice_phone
 * @property string $address_street_1
 * @property string $address_street_2
 * @property string $address_suburb
 * @property string $address_state
 * @property string $address_postcode
 * @property string $profession
 * @property string $skills
 * @property float $height
 * @property int $bha_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $last_login
 *
 * Attributes
 * @property Carbon $birthday
 * @property string $name
 * @property boolean $isSuperAdmin
 *
 * Relationships
 * @property Collection<Role> $roles
 * @property Collection<GroupMember> $mailing_list_memberships
 * @property Collection<Membership> $memberships
 * @property Collection<Enrolment> $enrolments
 * @property Membership $membership
 * @property Tenant $default_tenant
 */
class User extends Authenticatable implements HasMedia
{
    use Notifiable, InteractsWithMedia, SoftDeletes, HasFactory, TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'pronouns',
        'email',
        'password',
        'dob',
        'phone',
        'ice_name',
        'ice_phone',
        'address_street_1',
        'address_street_2',
        'address_suburb',
        'address_state',
        'address_postcode',
        'profession',
        'skills',
        'height',
        'bha_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $with = ['media', 'membership.roles'];

    public $dates = ['updated_at', 'created_at', 'last_login', 'dob'];

    protected $appends = ['name', 'avatar_url', 'profile_avatar_url', 'bha_type'];

    public $notify_channels = ['database', 'mail'];

    public static function create(array $attributes = [])
    {
        $attributes['password'] = self::setInitialPassword($attributes['password']);

        return static::query()->create($attributes);
    }

    public function update(array $attributes = [], array $options = [])
    {
        if (array_key_exists('password', $attributes) && ! $attributes['password']) {
            unset($attributes['password']);
        } else {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        parent::update($attributes, $options);

        if (isset($attributes['avatar'])) {
            $this->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }
        $this->save();

        return true;
    }

    public static function setInitialPassword(string $password = null): string
    {
        if (empty($password)) {
            return Str::random(10);
        }

        return Hash::make($password);
    }

    // @todo MAYBE move to singer
    // Mailing List memberships
    public function mailing_list_memberships(): MorphMany
    {
        return $this->morphMany(GroupMember::class, 'memberable');
    }

    // Organisation/Club memberships
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    // The membership for the current logged-in organisation
    public function membership(): HasOne
    {
        return $this->hasOne(Membership::class)
            ->ofMany(['id' => 'max'], function ($query) {
                $query->where('tenant_id', '=', tenancy()?->tenant?->id ?? null);
            });
    }

    public function enrolments(): HasManyThrough
    {
        return $this->hasManyThrough(Enrolment::class, Membership::class);
    }

    public function defaultTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'default_tenant_id');
    }

    public function scopeBirthdays(Builder $query): Builder
    {
        return $query->whereMonth('dob', '>=', now())
            ->whereMonth('dob', '<=', now()->addMonth());
    }

    public function getBirthdayAttribute(): Carbon
    {
        return $this->dob->copy()->year(now()->year);
    }

    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function bhaType(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) =>  $attributes['dob']
                ? Carbon::make($attributes['dob'])->diffInYears(now()->startOfYear()) > 25 ? 'Full' : 'Youth'
                : ''
        );
    }

    public function isSuperAdmin(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $attributes['email'] === 'hayleybech@gmail.com');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->useFallbackUrl('https://api.dicebear.com/7.x/initials/svg?seed='.$this->name.'&backgroundColor=200142&backgroundType=gradientLinear&fontSize=40')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(50)
                    ->height(50)
                    ->crop(Manipulations::CROP_CENTER, 50, 50);
                $this->addMediaConversion('profile')
                    ->width(200)
                    ->height(200)
                    ->crop(Manipulations::CROP_CENTER, 200, 200);
            });
    }

    public function getAvatarUrl(string $conversion): string
    {
        if (! $this->hasMedia('avatar')) {
            return $this->getFallbackMediaUrl('avatar');
        }

        if ($this->getFirstMedia('avatar')->hasGeneratedConversion($conversion)) {
            return $this->getFirstMediaUrl('avatar', $conversion);
        }

        return $this->getFirstMediaUrl('avatar');
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('avatar', 'thumb');
    }

    public function getProfileAvatarUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('avatar', 'profile');
    }

    // @todo make separate user vs singer welcome email
    public static function sendWelcomeEmail($user): void
    {
        // Generate a new reset password token
        $token = app('auth.password.broker')->createToken($user);

        // Send email
        Mail::send(new Welcome($user, $token));
    }
}
