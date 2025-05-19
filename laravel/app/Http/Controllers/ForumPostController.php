<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForumPost\ForumPostRequest;
use App\Http\Requests\ForumPost\ForumPostStoreRequest;
use App\Http\Requests\ForumPost\ForumPostUpdateRequest;
use App\Models\ForumPost;
use App\Services\General\ForumPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ForumPostController extends Controller
{
    public function __construct(
        protected ForumPostService $forumPostService
    ) {
        $this->authorizeResource(ForumPost::class, 'forumPost');
    }

    public function index(ForumPostRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->forumPostService->getForumPostsList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(ForumPostStoreRequest $request): JsonResponse
    {
        $forumTopicId = $request->post('forumTopicId');
        $content = $request->post('content');

        return $this->success($this->forumPostService->store(
            $forumTopicId,
            $content
        ), 201);
    }

    public function show(ForumPost $forumPost): JsonResponse
    {
        return $this->success($this->forumPostService->show($forumPost));
    }

    public function update(ForumPostUpdateRequest $request, ForumPost $forumPost): JsonResponse
    {
        $forumTopicId = $request->post('forumTopicId');
        $content = $request->post('content');

        return $this->success($this->forumPostService->update(
            $forumPost,
            $forumTopicId,
            $content
        ), 200);
    }

    public function destroy(ForumPost $forumPost): JsonResponse
    {
        return $this->success($this->forumPostService->destroy($forumPost));
    }
}
