@extends('layout')

@section('content')
<h1>Dashboard de Sincronización</h1>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Referencia</th>
            <th>Status</th>
            <th>Cambios</th>
            <th>Actual</th>
            <th>Nuevo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($diferencias as $diff)
            <tr>
                <td>{{ $diff['reference'] }}</td>
                <td>
                    @if($diff['status'] == 'NUEVO')
                        <span class="badge bg-success">Nuevo</span>
                    @elseif($diff['status'] == 'ELIMINADO')
                        <span class="badge bg-danger">Eliminado</span>
                    @elseif($diff['status'] == 'CAMBIOS')
                        <span class="badge bg-warning text-dark">Cambios</span>
                    @else
                        <span class="badge bg-secondary">Igual</span>
                    @endif
                </td>
                <td>{{ $diff['cambios'] }}</td>
                <td>
                    @if($diff['actual'])
                        {{ $diff['actual']->name }}<br>
                        Precio: {{ $diff['actual']->price }}<br>
                        Cantidad: {{ $diff['actual']->quantity }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($diff['nuevo'])
                        {{ $diff['nuevo']->name }}<br>
                        Precio: {{ $diff['nuevo']->price }}<br>
                        Cantidad: {{ $diff['nuevo']->quantity }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
