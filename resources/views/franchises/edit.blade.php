@extends('layout')

@section('content')
<h1>Editar Franquicia</h1>
<form method="POST" action="/franchises/{{ $model->id }}/update">
    {{ csrf_field() }}
    <div class="form-group">
        <label>Nombre:</label>
        <input type="text" name="name" class="form-control" value="{{ $model->name }}" required>
    </div>
    <div class="form-group">
        <label>Color:</label>
        <input type="color" name="color" class="form-control" value="{{ $model->color }}">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>
@endsection
