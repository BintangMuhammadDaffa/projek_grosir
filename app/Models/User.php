<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
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
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function operationalCosts()
    {
        return $this->hasMany(OperationalCost::class);
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function canAccessDashboard()
    {
        return $this->isOwner();
    }

    public function canAccessReports()
    {
        return $this->isOwner();
    }

    public function canAccessProfit()
    {
        return $this->isOwner();
    }

    public function canAccessOperationalCosts()
    {
        return $this->isOwner();
    }

    public function canAccessTransactionHistory()
    {
        return $this->isOwner();
    }

    public function canAccessCashier()
    {
        return true; // Both owner and staff can access cashier
    }

    public function canManageStock()
    {
        return true; // Both owner and staff can manage stock
    }

    public function canManageUsers()
    {
        return $this->isOwner();
    }
}
