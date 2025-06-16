@extends('layout')

@section('content')
<h1>Tiendas de la competencia</h1>
<div class="mb-3">
    <form method="GET" action="/competitor-stores" class="form-inline">
        <label>Año:</label>
        <select name="year" class="form-control mx-2">
            <option value="">Todos</option>
            @foreach($years as $y)
            <option value="{{$y}}" @if(isset($request->year) && $request->year==$y) selected @endif>{{$y}}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
</div>
<a href="/competitor-stores/create">Crear tienda</a>
<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Franquicia</th>
            <th>Año de apertura</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($model as $item)
        <tr>
            <td>{{$item->name}}</td>
            <td>@if(isset($item->franchise)){{$item->franchise->name}}@endif</td>
            <td>{{$item->opened_year}}</td>
            <td>
                <a href="/competitor-stores/{{$item->id}}/edit">Editar</a>
                <a href="/competitor-stores/{{$item->id}}/destroy" onclick="return confirm('¿Desea eliminar?')">Eliminar</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
