<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Office;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // Explicitly set the table name
    protected $table = 'tbl_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_initial',
        'last_name',
        'email',
        'password',
        'role_id',
        'office_id',
        'has_custom_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'tbl_user_permission', 'user_id', 'permission_id');
    }

    public function hasPermission($permissionName)
    {
        // Load permissions and role.permissions if not loaded to avoid N+1 and ensure we have data
        if (!$this->relationLoaded('permissions')) {
            $this->load('permissions');
        }
        
        if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        // If the user has customized permissions, use those as the source of truth
        if ($this->has_custom_permissions) {
            return $this->permissions->contains('name', $permissionName);
        }

        // Fallback to role permissions if no direct permissions are assigned
        return $this->role->permissions->contains('name', $permissionName);
    }
}
