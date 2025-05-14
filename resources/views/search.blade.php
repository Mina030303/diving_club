<!-- resources/views/search.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
  <h2>搜尋結果：{{ $query }}</h2>
  <br><br>

  @if($announcements->isEmpty() && $activities->isEmpty())
    <p>沒有找到相關內容。</p>
  @endif

  @if($announcements->isNotEmpty())
    <h4>公告</h4>
    <div class="card-group">
      @foreach($announcements as $item)
        <div class="card mb-3 p-3">
          <h5><a href="{{ route('announcements.show', $item->id) }}">{{ $item->title }}</a></h5>
          <p>{{ $item->content }}</p>
          <small class="text-muted">發布日期：{{ $item->created_at->format('Y-m-d') }}</small>
        </div>
      @endforeach
    </div>
  @endif

  @if($activities->isNotEmpty())
    <h4 class="mt-4">活動</h4>
    <div class="card-group">
      @foreach($activities as $item)
        <div class="card mb-3 p-3">
          <h5><a href="{{ route('activities.show', $item->id) }}">{{ $item->title }}</a></h5>
          <p>{{ $item->content }}</p>
          <small class="text-muted">舉辦日期：{{ $item->created_at->format('Y-m-d') }}</small>
        </div>
      @endforeach
    </div>
  @endif

  <h5 class="mt-5">最近搜尋</h5>

  @if($recentKeywords->isNotEmpty())
    <ul class="list-group">
      @foreach($recentKeywords as $keyword)
        <li class="list-group-item">
          <a href="{{ route('search', ['q' => $keyword]) }}">{{ $keyword }}</a>
        </li>
      @endforeach
    </ul>
  @else
    <p>（目前沒有搜尋紀錄）</p>
  @endif

  <form action="{{ route('clearSearchLogs') }}" method="POST" style="margin-top: 1rem;">
    @csrf
    <button type="submit" class="btn btn-sm btn-danger">清除搜尋紀錄</button>
  </form>

</div>
@endsection


