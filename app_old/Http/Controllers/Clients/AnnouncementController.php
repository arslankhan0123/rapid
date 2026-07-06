<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Models\Announcement;
use App\Repositories\AnnouncementRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * Class AnnouncementController
 */
class AnnouncementController extends AppBaseController
{
    /** @var AnnouncementRepository */
    private $announcementRepo;

    public function __construct(AnnouncementRepository $announcementRepo)
    {
        $this->announcementRepo = $announcementRepo;
    }

    /**
     * Display a listing of the Announcement.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('clients.announcements.index');
    }

    /**
     * Display the specified Announcement.
     *
     * @param  Announcement  $announcement
     * @return Factory|View
     */
    public function show(Announcement $announcement)
    {
        $announcement = $this->announcementRepo->getAnnouncementDetailClient($announcement->id);

        return view('clients.announcements.show', compact('announcement'));
    }
}
