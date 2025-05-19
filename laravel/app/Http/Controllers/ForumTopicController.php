<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForumTopic\ForumTopicRequest;
use App\Http\Requests\ForumTopic\ForumTopicStoreRequest;
use App\Http\Requests\ForumTopic\ForumTopicUpdateRequest;
use App\Models\ForumTopic;
use App\Services\General\ForumTopicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ForumTopicController extends Controller
{
    public function __construct(
        protected ForumTopicService $forumTopicService
    ) {
        $this->authorizeResource(ForumTopic::class, 'forumTopic');
    }

    public function index(ForumTopicRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->forumTopicService->getForumTopicsList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(ForumTopicStoreRequest $request): JsonResponse
    {
        $title = $request->post('title');
        $description = $request->post('description');

        return $this->success($this->forumTopicService->store(
            $title,
            $description
        ), 201);
    }

    public function show(ForumTopic $forumTopic): JsonResponse
    {
        return $this->success($this->forumTopicService->show($forumTopic));
    }

    public function update(ForumTopicUpdateRequest $request, ForumTopic $forumTopic): JsonResponse
    {
        $title = $request->post('title');
        $description = $request->post('description');

        return $this->success($this->forumTopicService->update(
            $forumTopic,
            $title,
            $description
        ), 200);
    }

    public function destroy(ForumTopic $forumTopic): JsonResponse
    {
        return $this->success($this->forumTopicService->destroy($forumTopic));
    }

    public function switchLock(ForumTopicUpdateRequest $request, ForumTopic $forumTopic): JsonResponse
    {
        $isLocked = $request->post('isLocked');

        return $this->success($this->forumTopicService->switchLock(
            $forumTopic,
            $isLocked
        ), 200);
    }
}
