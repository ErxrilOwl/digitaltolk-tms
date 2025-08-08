<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *  schema="Tag",
 *  type="object",
 *  @OA\Property(property="id", type="integer", example=1),
 *  @OA\Property(property="name", type="string", example="mobile"),
 *  @OA\Property(property="created_at", type="string", format="date-time"),
 *  @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *  schema="TagPagination",
 *  title="TagPagination",
 *  description="A paginated list of tags",
 *  @OA\Property(
 *      property="data",
 *      type="array",
 *      @OA\Items(ref="#/components/schemas/Tag")
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
class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function translation()
    {
        return $this->belongsToMany(Translation::class);
    }
}
