@extends('layout')

@section('content')
<h1>Franquicias</h1>
<div><a href="/franchises/create">+ Crear</a></div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Color</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($model as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>
                <span style="display:inline-block;width:20px;height:20px;background:{{ $item->color }};"></span>
                {{ $item->color }}
            </td>
            <td>
                <a href="/franchises/{{ $item->id }}/edit">Editar</a>
                <a href="/franchises/{{ $item->id }}/destroy" onclick="return confirm('¿Desea eliminar?')">Eliminar</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
