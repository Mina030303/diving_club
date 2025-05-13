<?php

// app/Http/Controllers/AnnouncementController.php
namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $pinnedAnnouncements = Announcement::where('is_published', true)
            ->where('is_pinned', true)
            ->orderBy('published_at', 'desc')
            ->get();
            
        $announcements = Announcement::where('is_published', true)
            ->where('is_pinned', false)
            ->orderBy('published_at', 'desc')
            ->paginate(10);
            
        return view('announcements.index', compact('pinnedAnnouncements', 'announcements'));
    }
    
    public function show(Announcement $announcement)
    {
        if (!$announcement->is_published) {
            abort(404);
        }
        return view('announcements.show', compact('announcement'));
    }

}