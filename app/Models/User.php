<?php

namespace App\Models;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Role;
use App\Models\UserProfile;
use App\Models\Teacher;
use App\Models\Student;

/**
 * @property Role $role
 */

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role->name,
            'permissions' => $this->getAllPermissions()
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function getAllPermissions()
    {
        return optional($this->role->permissions) ->pluck('name')->toArray() ?? [];
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->getAllPermissions());
    }

    public function hasRole($roleName)
    {
        return $this->role->name === $roleName;
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('superadmin');
    }
}
