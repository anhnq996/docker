<?php

namespace App\Models;

use App\Enums\FileType;
use App\Models\Attributes\PathAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, PathAttribute;

    protected $table = 'files';

    protected $fillable = [
        'path',
        'type',
        'game_id',
    ];

    protected $casts = [
        'type'    => FileType::class,
        'game_id' => 'integer',
        'path'    => 'string',
    ];

    public function list($id)
    {
        return $this->select([
            'id', 'path', 'type', 'game_id'
        ])->when(!empty($id), function ($query) use ($id) {
            $query->where('game_id', $id);
        })->get();
    }
}
