@extends('layouts.web')

@section('title', ($article->title ?? 'Article') . ' — Laravel CI')

@section('content')
    @livewire('blog.article-detail', ['slug' => $slug])
@endsection
