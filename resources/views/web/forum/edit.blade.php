@extends('layouts.web')

@section('title', 'Modifier la question — Laravel CI')

@section('content')
    @livewire('forum.edit-question', ['question' => $question])
@endsection
