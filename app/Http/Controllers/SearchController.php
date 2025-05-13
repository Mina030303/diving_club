<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\SearchLog;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $sort = $request->input('sort');

        // 儲存搜尋紀錄
        if ($query) {
            SearchLog::create(['keyword' => $query]);
        }

        // 搜尋活動與公告
        $announcements = Announcement::search($query)->get();
        $activities = Activity::search($query)->get();

        // 排序：若選擇「最新」
        if ($sort === 'newest') {
            $announcements = $announcements->sortByDesc('created_at');
            $activities = $activities->sortByDesc('created_at');
        }

        // 撈出最近的搜尋關鍵字（最新 5 筆）
        $recentKeywords = SearchLog::latest()->limit(5)->pluck('keyword');

        return view('search', [
            'query' => $query,
            'sort' => $sort,
            'announcements' => $announcements,
            'activities' => $activities,
            'recentKeywords' => $recentKeywords,
        ]);
    }

    public function clearSearchLogs()
    {
        SearchLog::query()->delete();

        return redirect()->route('search')->with('message', '搜尋紀錄已清除！');
    }
}