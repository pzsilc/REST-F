@extends('layout')
@section('main')
    <div class="text-center mx-auto" style="width: 80%; max-width: 800px;">
        <h1 class="magenta my-5 py-5">Lista ankiet</h1>
        <ul class="list-group">
        @foreach($questionnaires as $questionnaire)
            <li class="list-group-item list-element">
                <a href="{{ $app_path }}/questionnaires/single?id={{ $questionnaire->id }}" class="d-flex justify-content-between">
                    <div style="font-weight: bold;">{{ $questionnaire->title }}</div>
                    <div>{{ $questionnaire->created_at }}</div>
                </a>
            </li>
        @endforeach
        </ul>
    </div>
@endsection