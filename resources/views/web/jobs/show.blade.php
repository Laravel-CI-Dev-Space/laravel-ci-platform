@extends("layouts.web")

@section("title", ($offer->title ?? "Offre") . " — Laravel CI")

@section("content")
    @livewire("jobs.job-detail", ["slug" => $slug])
@endsection
