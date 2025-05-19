<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForumComment\ForumCommentRequest;
use App\Http\Requests\ForumComment\ForumCommentStoreRequest;
use App\Http\Requests\ForumComment\ForumCommentUpdateRequest;
use App\Models\ForumComment;
use App\Services\General\ForumCommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ForumCommentController extends Controller
{
    public function __construct(
        protected ForumCommentService $forumCommentService
    ) {
        $this->authorizeResource(ForumComment::class, 'forumComment');
    }

    public function index(ForumCommentRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->forumCommentService->getForumCommentsList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(ForumCommentStoreRequest $request): JsonResponse
    {
        $forumPostId = $request->post('forumPostId');
        $content = $request->post('content');

        return $this->success($this->forumCommentService->store(
            $forumPostId,
            $content
        ), 201);
    }

    public function show(ForumComment $forumComment): JsonResponse
    {
        return $this->success($this->forumCommentService->show($forumComment));
    }

    public function update(ForumCommentUpdateRequest $request, ForumComment $forumComment): JsonResponse
    {
        $forumPostId = $request->post('forumPostId');
        $content = $request->post('content');

        return $this->success($this->forumCommentService->update(
            $forumComment,
            $forumPostId,
            $content
        ), 200);
    }

    public function destroy(ForumComment $forumComment): JsonResponse
    {
        return $this->success($this->forumCommentService->destroy($forumComment));
    }
}
