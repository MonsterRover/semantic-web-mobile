<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skripsi extends Model
{
    use HasFactory;

    protected $table = 'skripsi';

    protected $fillable = [
        'judul',
        'abstrak',
        'kata_kunci',
        'topik',
        'tahun',
        'penulis',
        'pembimbing',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'created_by',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'file_size' => 'integer',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_size) return null;
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    // Scopes
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('kata_kunci', 'like', "%{$keyword}%")
              ->orWhere('abstrak', 'like', "%{$keyword}%")
              ->orWhere('topik', 'like', "%{$keyword}%");
        });
    }

    public function scopeByTopik($query, $topik)
    {
        if ($topik) {
            return $query->where('topik', 'like', "%{$topik}%");
        }
        return $query;
    }

    public function scopeByTahun($query, $tahun)
    {
        if ($tahun) {
            return $query->where('tahun', $tahun);
        }
        return $query;
    }
}
