@extends('layout')
@section('main')
    <div>
        <h1 class='magenta text-center mb-5 pb-5'>{{ $questionnaire->title }}</h1>
        <ol class='mb-5 pb-5'>
            @foreach($questionnaire->get_questions() as $question)
                <div class='mb-5'>
                    <div class='d-flex justify-content-between'>
                        <h3>{{ $question->content }}</h3>
                        <div>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal_{{ $question->id }}">Edytuj</button>  
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal_{{ $question->id }}">Usuń</button>
                        </div>
                        <div class="modal fade" id="editModal_{{ $question->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <form method="POST" action="{{ $app_path }}/dashboard/questionnaires/questions/edit" class="modal-dialog" role="document">
                                {!! $csrf !!}  
                                <input type="hidden" value="{{ $question->id }}" name="question_id"/>
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Edytuj pytanie</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="text" value="{{ $question->content }}" max="256" class="form-control" name="content" required/>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                                        <button type="submit" class="btn btn-primary">Zapisz</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal fade" id="deleteModal_{{ $question->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <form method="POST" action="{{ $app_path }}/dashboard/questionnaires/questions/delete" class="modal-dialog" role="document">
                                {!! $csrf !!}    
                                <input type="hidden" value="{{ $question->id }}" name="question_id"/>
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Usuń pytanie</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Czy na pewno chcesz usunąć te pytanie?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                                        <button type="submit" class="btn btn-primary">Usuń</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr/>
                    @if($question->get_answers())
                        <ul class='mt-5'>
                            @foreach($question->get_answers() as $answer)
                                <li class='mb-3'>{{ $answer->content }}</li>   
                            @endforeach
                        </ul>
                    @else
                        <p class='text-muted'>Brak odpowiedzi</p>
                    @endif
                </div>
            @endforeach
        </ol>
        <form method="POST" action="{{ $app_path }}/dashboard/questionnaires/questions/add?questionnaire_id={{ $questionnaire->id }}">
            {!! $csrf !!}
            <h4 class='text-muted'>Dodaj pytanie</h4>
            <input type="text" class="form-control" max="256" name="content" required/>
            <input type="submit" class="btn btn-primary mt-2"/>
        </form>
    </div>
@endsection