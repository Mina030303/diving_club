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
            $data = ['keyword' => $query];

            if (auth()->check()) {
                $data['user_id'] = auth()->id();
            } else {
                $data['session_id'] = session()->getId();
            }

            SearchLog::create($data);
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
        $recentKeywords = collect(); // 預設空集合，避免未定義錯誤

        if (auth()->check()) {
            $recentKeywords = SearchLog::where('user_id', auth()->id())
                ->latest()
                ->limit(5)
                ->pluck('keyword');
        } else {
            $recentKeywords = SearchLog::where('session_id', session()->getId())
                ->latest()
                ->limit(5)
                ->pluck('keyword');
        }

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
        if (auth()->check()) {
            SearchLog::where('user_id', auth()->id())->delete();
        }else {
            SearchLog::where('session_id', session()->getId())->delete();
        }

        return redirect()->route('search')->with('message', '用戶搜尋紀錄已清除');
    }

}