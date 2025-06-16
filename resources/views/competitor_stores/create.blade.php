@extends('layout')

@section('content')
<h1>Nueva Tienda Competencia</h1>
<form method="POST" action="/competitor-stores">
    {{ csrf_field() }}
    <div class="form-group">
        <label>Nombre:</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Franquicia:</label>
        <select name="franchise_id" class="form-control">
            <option value="">-- Ninguna --</option>
            @foreach($franchises as $f)
            <option value="{{$f->id}}">{{$f->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Año de apertura:</label>
        <input type="number" name="opened_year" class="form-control" placeholder="2020">
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
@endsection
