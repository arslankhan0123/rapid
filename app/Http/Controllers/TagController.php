<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TagController extends AppBaseController
{
    /** @var TagRepository */
    private $tagRepository;

    public function __construct(TagRepository $tagRepo)
    {
        $this->tagRepository = $tagRepo;
    }

    /**
     * Display a listing of the Tag.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('tags.index');
    }

    /**
     * Store a newly created Tag in storage.
     *
     * @param  CreateTagRequest  $request
     * @return JsonResponse
     */
    public function store(CreateTagRequest $request)
    {
        $input = $request->all();

        $tag = $this->tagRepository->create($input);

        activity()->performedOn($tag)->causedBy(getLoggedInUser())
            ->useLog('New Tag created.')->log($tag->name.' Tag created.');

        return $this->sendResponse($tag, __('messages.tag.tag_saved_successfully'));
    }

    /**
     * @param  Tag  $tag
     * @return mixed
     */
    public function show(Tag $tag)
    {
        return $this->sendResponse($tag, 'Tag retrieved successfully.');
    }

    /**
     * Show the form for editing the specified Tag.
     *
     * @param  Tag  $tag
     * @return JsonResponse
     */
    public function edit(Tag $tag)
    {
        return $this->sendResponse($tag, 'Tag retrieved successfully.');
    }

    /**
     * Update the specified Tag in storage.
     *
     * @param  UpdateTagRequest  $request
     * @param  Tag  $tag
     * @return JsonResponse
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $input = $request->all();

        $tag = $this->tagRepository->update($input, $tag->id);

        activity()->performedOn($tag)->causedBy(getLoggedInUser())
            ->useLog('Tag updated.')->log($tag->name.' Tag updated.');

        return $this->sendSuccess(__('messages.tag.tag_updated_successfully'));
    }

    /**
     * Remove the specified Tag from storage.
     *
     * @param  Tag  $tag
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Tag $tag)
    {
        activity()->performedOn($tag)->causedBy(getLoggedInUser())->useLog('Tag deleted.')->log($tag->name.' Tag deleted.');

        $tag->delete();

        return $this->sendSuccess('Tag deleted successfully.');
    }
}
