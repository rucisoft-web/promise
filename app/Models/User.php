<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    public function resource(): HasOne
    {
        return $this->hasOne(Resource::class);
    }

    public function isGuestVisitor(): bool
    {
        return $this->role === 'tamu';
    }

    /** Admin & PMO mengelola seluruh portofolio. */
    public function managesAll(): bool
    {
        return in_array($this->role, ['admin', 'pmo'], true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Boleh mengubah data proyek (piagam, WBS, risiko, dst.). */
    public function canEditProjects(): bool
    {
        return in_array($this->role, ['admin', 'pmo', 'manajer'], true);
    }

    public function canEditProject(?Project $project): bool
    {
        if ($this->managesAll()) {
            return true;
        }

        return $this->role === 'manajer' && $project && (int) $project->manager_id === (int) $this->id;
    }
}
