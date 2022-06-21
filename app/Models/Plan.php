<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int id
 * @property string name
 * @property int price
 * @property object properties
 * @property Carbon created_at
 * @property Carbon updated_at
*/
class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'properties',
        'duration_time',
        'created_at',
        'updated_at',
        'is_best_seller',
    ];

    protected $casts = [
        'properties'     => 'array',
        'is_best_seller' => 'boolean',
    ];

    public function list($request, $paginate = true)
    {
        $keyword   = $request->get('keyword');
        $limit     = $request->get('limit') ?? 20;
        $toPrice   = $request->get('to_price') ?? null;
        $fromPrice = $request->get('from_price') ?? null;

        $query = $this->select([
            'id', 'name', 'price', 'properties', 'duration_time', 'is_best_seller',
        ])
            ->when($keyword, function ($query) use ($keyword) {
                $keyword = '%' . $keyword . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', $keyword);
                });
            })
            ->when($toPrice, function ($query) use ($toPrice) {
                $query->where('price', '>=', $toPrice);
            })
            ->when($fromPrice, function ($query) use ($fromPrice) {
                $query->where('price', '<=', $fromPrice);
            })
            ->orderBy('is_best_seller', 'desc')
            ->orderBy('id', 'desc');

        if ($paginate) {
            return $query->paginate($limit);
        } else {
            return $query->get();
        }
    }
}
