<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'source',
        'source_other_site',
        'source_other',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_last_login' => 'datetime',
        'crossref_date' => 'datetime',
        'password' => 'hashed',
    ];

    public function fullName(bool $lastNameFirst = false): string
    {
        if ($lastNameFirst) {
            return ($this->last_name != '*' ? $this->last_name.', ' : '').$this->first_name.($this->middle_name ? ' '.$this->middle_name : '');
        } else {
            return $this->first_name.' '.($this->middle_name ? $this->middle_name.' ' : '').($this->last_name != '*' ? $this->last_name : '');
        }
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(Conversion::class);
    }

    public function requiredResponses(): HasMany
    {
        return $this->hasMany(RequiredResponse::class);
    }
}
