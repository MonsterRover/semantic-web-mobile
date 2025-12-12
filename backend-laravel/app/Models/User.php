<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function skripsi()
    {
        return $this->hasMany(Skripsi::class, 'created_by');
    }

    public function ontologies()
    {
        return $this->hasMany(Ontology::class, 'uploaded_by');
    }

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKaprodi()
    {
        return $this->role === 'kaprodi';
    }

    public function isMahasiswa()
    {
        return $this->role === 'mahasiswa';
    }
}
