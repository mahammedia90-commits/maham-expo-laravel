<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Get user's favorites
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $query = Favorite::with('favoritable')
            ->forUser($userId);

        // Filter by type
        if ($type = $request->input('type')) {
            match($type) {
                'event' => $query->events(),
                'space' => $query->spaces(),
                default => null,
            };
        }

        $favorites = $query->latest()->paginate(15);

        return ApiResponse::paginated(
            $favorites->through(fn($item) => new FavoriteResource($item))
        );
    }

    /**
     * Add to favorites / Toggle favorite
     */
    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $result = Favorite::toggle(
            $userId,
            $request->type,
            $request->id
        );

        if (isset($result['error'])) {
            return ApiResponse::error(
                $result['error'],
                ApiErrorCode::INVALID_INPUT
            );
        }

        $message = $result['added']
            ? __('messages.favorite.added')
            : __('messages.favorite.removed');

        return ApiResponse::success([
            'is_favorited' => $result['added'],
        ], $message);
    }

    /**
     * Remove from favorites
     */
    public function destroy(Request $request, Favorite $favorite): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this favorite
        if ($favorite->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        $favorite->delete();

        return ApiResponse::success(
            null,
            __('messages.favorite.removed')
        );
    }
}
