@extends('layout')
@section('main')
    <form method="POST" class="p-5">
        {!! $csrf !!}
        <h3 class='text-muted mb-3 text-center'>Zaloguj się</h3>
        <p class='mb-1'>Token</p>
        <input type="password" name="token" required class="form-control" />
        <input type="submit" class="btn btn-primary mt-2" style="width:100%;" />
    </form>
@endsection