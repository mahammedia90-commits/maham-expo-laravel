<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSectionRequest;
use App\Http\Requests\Admin\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Event;
use App\Models\Section;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Get event sections (admin)
     */
    public function index(Request $request, Event $event): JsonResponse
    {
        $query = $event->sections();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $sections = $query->ordered()->get();

        return ApiResponse::success(
            SectionResource::collection($sections)
        );
    }

    /**
     * Create section for event
     */
    public function store(StoreSectionRequest $request, Event $event): JsonResponse
    {
        $data = $request->validated();
        $data['event_id'] = $event->id;

        $section = Section::create($data);

        return ApiResponse::created(
            new SectionResource($section),
            __('messages.section.created')
        );
    }

    /**
     * Get single section
     */
    public function show(Section $section): JsonResponse
    {
        return ApiResponse::success(
            new SectionResource($section)
        );
    }

    /**
     * Update section
     */
    public function update(UpdateSectionRequest $request, Section $section): JsonResponse
    {
        $section->update($request->validated());

        return ApiResponse::success(
            new SectionResource($section),
            __('messages.section.updated')
        );
    }

    /**
     * Delete section
     */
    public function destroy(Section $section): JsonResponse
    {
        // Check if section has spaces
        $hasSpaces = $section->spaces()->exists();

        if ($hasSpaces) {
            return ApiResponse::error(
                __('messages.section.has_spaces'),
                'section_has_spaces'
            );
        }

        $section->delete();

        return ApiResponse::success(
            null,
            __('messages.section.deleted')
        );
    }
}
