@extends('layout')
@section('main')
    <section>
        <h1 class='text-muted'>Lista ankiet</h1>
        <ul class='list-group'>
        @foreach($questionnaires as $questionnaire)
            <li class='list-group-item list-element'>
                <div class='d-flex justify-content-between'>
                    <div>
                        <a href="{{ $app_path }}/dashboard/questionnaires/single?id={{ $questionnaire->id }}"
                            style="position: relative; top: 6px;"
                        >
                            <i class='fa fa-eye magenta mr-2'></i>
                            {{ $questionnaire->title }} 
                            <span class='ml-3 text-muted'>| {{ $questionnaire->created_at }}</span>
                        </a>
                        <span class='magenta copy-url' style="position: relative; top: 6px;">
                            <input type='hidden' value="{{ $app_path }}/questionnaires/single?id={{ $questionnaire->id }}"/>
                            <i class='fa fa-clone mr-2 ml-5'></i>
                            Kopiuj link do ankiety
                        </span>
                    </div>
                    <div class='d-flex justify-content-between'>
                        <button type='button' class='btn btn-primary mr-1' data-toggle="modal" data-target="#editModal_{{ $questionnaire->id }}">Edytuj</button>
                        <button type='button' class='btn btn-danger' data-toggle="modal" data-target="#deleteModal_{{ $questionnaire->id }}">Usuń</button>
                    </div>
                    <div class="modal fade" id="editModal_{{ $questionnaire->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <form method="POST" action="{{ $app_path }}/dashboard/questionnaires/edit" class="modal-dialog" role="document">
                            {!! $csrf !!}    
                            <input type='hidden' value='{{ $questionnaire->id }}' name='questionnaire_id'/>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edytuj pytanie</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type='text' class='form-control' value='{{ $questionnaire->title }}' max='64' name='title' required/>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                                    <button type='submit' class='btn btn-primary'>Edytuj</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal fade" id="deleteModal_{{ $questionnaire->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <form method="POST" action="{{ $app_path }}/dashboard/questionnaires/delete" class="modal-dialog" role="document">
                            {!! $csrf !!}    
                            <input type='hidden' value='{{ $questionnaire->id }}' name='questionnaire_id'/>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Usuń pytanie</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Czy na pewno chcesz usunąć tę ankietę?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                                    <button type='submit' class='btn btn-primary'>Usuń</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </li>
        @endforeach
        </ul>
    </section>
    <section class="mt-5 pt-5">
        <form method="POST" action="{{ $app_path }}/dashboard/questionnaires/add">
            {!! $csrf !!}
            <h1 class="text-muted mb-3">Dodaj nową ankietę</h1>
            <input type="text" name="title" class="form-control" required/>
            <input type="submit" class="btn btn-primary mt-2" style="width:100%;"/>
        </form>
    </section>
@endsection