<?php

namespace App\Http\Controllers\API;

use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Languages",
 *     description="Everything about languages"
 * )
 */
class LanguageController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/languages",
     *     operationId="getLanguages",
     *     tags={"Languages"},
     *     summary="Get all languages",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="Filter by code",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LanguagePagination")
     *     )
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $languages = Language::filter(request(['code', 'name']))
            ->orderBy('name')
            ->paginate(10);


        return response()->json($languages);
    }

    /**
     * @OA\Get(
     *      path="/api/languages/{language}",
     *      operationId="getLanguageById",
     *      tags={"Languages"},
     *      summary="Get a single language by ID",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="language",
     *          in="path",
     *          required=true,
     *          description="The ID of the language to retrieve",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Language")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Language not found"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     * @param App\Models\Language $language
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Language $language): JsonResponse
    {
        return $this->sendResponse($language, "Language successfully fetched");
    }

    /**
     * @OA\Post(
     *      path="/api/languages",
     *      operationId="createLanguage",
     *      tags={"Languages"},
     *      summary="Create a new language",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"code", "name"},
     *              @OA\Property(property="code", type="string", description="The ISO 639-1 code for the language"),
     *              @OA\Property(property="name", type="string", description="The full name of the language")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Language created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Language created successfully"),
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Language")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => 'required|unique:languages,code',
            'name' => 'required'
        ]);

        return $this->sendResponse(Language::create($data), "Language created successfully", 201);
    }

    /**
     * @OA\Put(
     *      path="/api/languages/{language}",
     *      operationId="updateLanguage",
     *      tags={"Languages"},
     *      summary="Update an existing language",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="language",
     *          in="path",
     *          required=true,
     *          description="The ID of the language to update",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"code", "name"},
     *              @OA\Property(property="code", type="string", description="The new ISO 639-1 code for the language"),
     *              @OA\Property(property="name", type="string", description="The new full name of the language")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Language updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Language updated successfully"),
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Language")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Language not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     * @param App\Models\Language $language
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Language $language, Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', Rule::unique(Language::class)->ignore($language->id)],
            'name' => 'required'
        ]);

        $language->update($data);

        return $this->sendResponse($language, "Language updated successfully");
    }

    /**
     * @OA\Delete(
     *      path="/api/languages/{language}",
     *      operationId="deleteLanguage",
     *      tags={"Languages"},
     *      summary="Delete a language by ID",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="language",
     *          in="path",
     *          required=true,
     *          description="The ID of the language to delete",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Language deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data"),
     *              @OA\Property(property="message", type="string", example="Language deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Language not found"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     * @param App\Models\Language $language
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Language $language): JsonResponse
    {
        $language->delete();

        return $this->sendResponse(null, "Language deleted successfully", 204);
    }
}
