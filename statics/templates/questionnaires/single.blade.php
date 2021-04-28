@extends('layout')
@section('main')
    <form method="POST" action="{{ $app_path }}/questionnaires/answers/store?questionnaire_id={{ $questionnaire->id }}" class="card p-5 mt-5 rounded-0">
        <div class='markers d-flex justify-content-between'>
            <div></div>
            <div></div>
        </div>
        {!! $csrf !!}
        <h1 class="magenta mb-5 text-center">{{ $questionnaire->title }}</h1>
        @foreach($questions as $question)
            <div>
                <div>
                    <h3 class='mb-3 mt-5'>{{ $question->content }}</h3>
                    <textarea name="q_{{ $question->id }}" placeholder="Uzupełnij" class="form-control" required></textarea>
                </div>
            </div>
        @endforeach
        <input type='submit' class='btn btn-primary mt-5'/>
    </form>
@endsection