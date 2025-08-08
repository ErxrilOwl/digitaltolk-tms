<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *  schema="Language",
 *  type="object",
 *  @OA\Property(property="id", type="integer", example=1),
 *  @OA\Property(property="code", type="string", example="en"),
 *  @OA\Property(property="name", type="string", example="English"),
 *  @OA\Property(property="created_at", type="string", format="date-time"),
 *  @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *  schema="LanguagePagination",
 *  title="LanguagePagination",
 *  description="A paginated list of languages",
 *  @OA\Property(
 *      property="data",
 *      type="array",
 *      @OA\Items(ref="#/components/schemas/Language")
 *  ),
 *  @OA\Property(
 *      property="links",
 *      type="object",
 *      @OA\Property(
 *          property="first",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="last",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="prev",
 *          type="string",
 *          nullable=true
 *      ),
 *      @OA\Property(
 *          property="next",
 *          type="string",
 *          nullable=true
 *      )
 * ),
 * @OA\Property(
 *      property="current_page",
 *      type="integer"
 * ),
 * @OA\Property(
 *      property="from",
 *      type="integer"
 * ),
 * @OA\Property(
 *      property="last_page",
 *      type="integer"
 * ),
 * @OA\Property(
 *      property="path",
 *      type="string"
 * ),
 * @OA\Property(
 *      property="per_page",
 *      type="integer"
 * ),
 * @OA\Property(
 *      property="to",
 *      type="integer"
 * ),
 * @OA\Property(
 *      property="total",
 *      type="integer"
 *  )
 * )
 */
class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name'
    ];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    public function scopeFilter($query, array $filters)
    {
        if ($filters['code'] ?? false) {
            $query->where('code', $filters['code']);
        }

        if ($filters['name'] ?? false) {
            $query->where('name', "LIKE", "%{$filters['name']}%");
        }
    }
}
