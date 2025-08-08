<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *  schema="Translation",
 *  type="object",
 *  @OA\Property(property="id", type="integer", example=1),
 *  @OA\Property(property="key", type="string", example="hello"),
 *  @OA\Property(property="value", type="string", example="Hello"),
 *  @OA\Property(property="language_id", type="integer", example=1),
 *  @OA\Property(property="created_at", type="string", format="date-time"),
 *  @OA\Property(property="updated_at", type="string", format="date-time"),
 *  @OA\Property(
 *      property="tags",
 *      type="array",
 *      @OA\Items(ref="#/components/schemas/Tag")
 *  )
 * )
 * @OA\Schema(
 *  schema="TranslationPagination",
 *  title="TranslationPagination",
 *  description="A paginated list of translations",
 *  @OA\Property(
 *      property="data",
 *      type="array",
 *      @OA\Items(ref="#/components/schemas/Translation")
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
 *      property="meta",
 *      type="object",
 *      @OA\Property(
 *          property="current_page",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="from",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="last_page",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="per_page",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="to",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="total",
 *          type="integer"
 *      )
 *  )
 * )
 */
class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'language_id'
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeFilter($query, array $filters)
    {
        if ($filters['tag_ids'] ?? false) {
            $query->whereHas('tags', function($sq) {
                $tag_ids = explode(',', request('tag_ids'));
                $sq->where('id', $tag_ids);
            });
        }

        if ($filters['keys'] ?? false) {
            $keys = explode(',', request('keys'));
            $query->whereIn('key', $keys);
        }

        if ($filters['value'] ?? false) {
            $query->where('value', 'LIKE', "%{$filters['value']}%");
        }
    }
}
