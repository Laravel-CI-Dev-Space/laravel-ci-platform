@extends('layouts.web')

@section('title', $slug . ' — Événements — Laravel CI')

@section('content')
  @livewire('events.event-detail', ['slug' => $slug])
@endsection
