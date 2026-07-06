<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CommentController extends AppBaseController
{
    /** @var CommentRepository */
    private $commentRepository;

    public function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepository = $commentRepo;
    }

    /**
     * Display a listing of the Comment.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('comments.index');
    }

    /**
     * Store a newly created Comment in storage.
     *
     * @param  CreateCommentRequest  $request
     * @return JsonResponse
     */
    public function store(CreateCommentRequest $request)
    {
        $input = $request->all();

        $comment = $this->commentRepository->create($input);

        return $this->sendResponse($comment, __('messages.comment.comments_saved_successfully'));
    }

    /**
     * Show the form for editing the specified Comment.
     *
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function edit(Comment $comment)
    {
        return $this->sendResponse($comment, 'Comment retrieved successfully.');
    }

    /**
     * Update the specified Comment in storage.
     *
     * @param  Comment  $comment
     * @param  UpdateCommentRequest  $request
     * @return JsonResponse
     */
    public function update(Comment $comment, UpdateCommentRequest $request)
    {
        $comment = $this->commentRepository->update($request->all(), $comment->id);

        activity()->performedOn($comment)->causedBy(getLoggedInUser())
            ->useLog('Comment updated.')
            ->log($comment->description.' Comment updated.');

        return $this->sendSuccess(__('messages.comment.comments_updated_successfully'));
    }

    /**
     * Remove the specified Comment from storage.
     *
     * @param  Comment  $comment
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Comment $comment)
    {
        activity()->performedOn($comment)->causedBy(getLoggedInUser())
            ->useLog('Comment deleted.')
            ->log($comment->description.' Comment deleted.');

        $this->commentRepository->delete($comment->id);

        return $this->sendSuccess('Comment deleted successfully.');
    }
}
