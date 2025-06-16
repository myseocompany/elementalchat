@extends('layout')

@section('content')
<h1>Editar Tienda Competencia</h1>
<form method="POST" action="/competitor-stores/{{$model->id}}/update">
    {{ csrf_field() }}
    <div class="form-group">
        <label>Nombre:</label>
        <input type="text" name="name" class="form-control" value="{{$model->name}}" required>
    </div>
    <div class="form-group">
        <label>Franquicia:</label>
        <select name="franchise_id" class="form-control">
            <option value="">-- Ninguna --</option>
            @foreach($franchises as $f)
            <option value="{{$f->id}}" @if($model->franchise_id==$f->id) selected @endif>{{$f->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Año de apertura:</label>
        <input type="number" name="opened_year" class="form-control" value="{{$model->opened_year}}">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>
@endsection
