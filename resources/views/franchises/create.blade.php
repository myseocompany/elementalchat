@extends('layout')

@section('content')
<h1>Nueva Franquicia</h1>
<form method="POST" action="/franchises">
    {{ csrf_field() }}
    <div class="form-group">
        <label>Nombre:</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Color:</label>
        <input type="color" name="color" class="form-control" value="#000000">
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
@endsection
