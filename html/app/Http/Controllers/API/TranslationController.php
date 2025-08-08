<?php

namespace App\Http\Controllers\API;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *  name="Translations",
 *  description="Everything about translations"
 * )
 */
class TranslationController extends BaseController
{
    /**
     * @OA\Get(
     *  path="/api/translations",
     *  operationId="getTranslations",
     *  tags={"Translations"},
     *  summary="Get all translations with pagination, supporting search filters for multiple tags, keys and value",
     *  security={{"sanctum":{}}},
     *  @OA\Parameter(
     *      name="tag_ids",
     *      in="query",
     *      description="Filter by tag id(s). Provide multiple tag ids separated by commas (e.g., 1,2)",
     *      required=false,
     *      @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      description="Filter by key(s). Provide multiple keys separated by commas (e.g., 'hello,hi')",
     *      required=false,
     *      @OA\Schema(type="string")
     *  ),
     * @OA\Parameter(
     *      name="name",
     *      in="query",
     *      description="Filter by key(s). Provide multiple keys separated by commas (e.g., 'hello,hi')",
     *      required=false,
     *      @OA\Schema(type="string")
     * ),
     * @OA\Response(
     *      response=200,
     *      description="Successful operation",
     *      @OA\JsonContent(ref="#/components/schemas/TranslationPagination")
     * ),
     * @OA\Response(
     *      response=404,
     *      description="No translations found"
     *  )
     * )
     */
    public function index(): Response
    {
        $translations = Translation::with('tags')->filter(request(['tag_ids', 'keys', 'value']))
            ->paginate(10);

        return response($translations);
    }

    /**
     * @OA\Get(
     *    path="/api/translations/{id}",
     *    operationId="getTranslationById",
     *    tags={"Translations"},
     *    summary="Get translation by ID",
     *    security={{"sanctum":{}}},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the translation to return",
     *         @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Translation fetched successfully"),
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Translation")
     *         )
     *    ),
     *    @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *    )
     * )
     * @param App\Models\Translation $translation
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Translation $translation): JsonResponse
    {
        return $this->sendResponse($translation, "Translation fetched successfully");
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     operationId="createTranslation",
     *     tags={"Translations"},
     *     summary="Create a new translation",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key", "value", "language_id"},
     *             @OA\Property(property="key", type="string", example="greeting"),
     *             @OA\Property(property="value", type="string", example="Hello"),
     *             @OA\Property(property="language_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Translation"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translation created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => 'required',
            'value' => 'required',
            'language_id' => 'required'
        ]);

        Cache::clear('translations_' . $data['language_id']);
        return $this->sendResponse(Translation::create($data), "Translation created successfully", 201);
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     operationId="updateTranslation",
     *     tags={"Translations"},
     *     summary="Update an existing translation",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the translation to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key", "value", "language_id"},
     *             @OA\Property(property="key", type="string", example="greeting"),
     *             @OA\Property(property="value", type="string", example="Hello"),
     *             @OA\Property(property="language_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Translation"),
     *             @OA\Property(property="message", type="string", example="Translation updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     * @param App\Models\Translation $translation
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Translation $translation, Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => 'required',
            'value' => 'required',
            'language_id' => 'required'
        ]);

        $translation->update($data);
        Cache::clear('translations_' . $data['language_id']);
        return $this->sendResponse($translation, "Translation updated successfully");
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     operationId="deleteTranslation",
     *     tags={"Translations"},
     *     summary="Delete a translation",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the translation to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Translation deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     * @param App\Models\Translation $translation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Translation $translation): JsonResponse
    {
        $translation->delete();

        return $this->sendResponse(null, "Translation deleted successfully", 204);
    }

    /**
     * @OA\Get(
     *      path="/api/translations/export/{locale}",
     *      operationId="exportTranslations",
     *      tags={"Translations"},
     *      summary="Export all translations for a specific language as JSON",
     *      security={{"sanctum":{}}},
     *      description="Returns all translations for a given locale in a key-value JSON format.",
     * @OA\Parameter(
     *      name="locale",
     *      in="path",
     *      description="The locale code of the language (e.g., 'en', 'fr')",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     * ),
     * @OA\Response(
     *      response=200,
     *      description="Successful export",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *              "greeting": "Hello",
     *              "farewell": "Goodbye"
     *          }
     *      )
     * ),
     * @OA\Response(
     *      response=404,
     *      description="Language not found",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Language not found")
     *      )
     *  )
     * )
     * @param string $locale
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(string $locale): JsonResponse
    {
        $language = Language::where('code', $locale)->first();

        if (!$language) {
            return $this->sendError('Language not found', []);
        }

        $cacheKey = 'translations_' . $language->id;
        $translations = Cache::get($cacheKey);

        if (!$translations) {
            $translations = Translation::where('language_id', $language->id)->pluck('value', 'key');
            Cache::put($cacheKey, $translations, now()->addHours(24));
        }

        return response()->json($translations);
    }
}
