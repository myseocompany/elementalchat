@extends('layout')

@section('content')
<h1>Tiendas de la competencia</h1>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
    .brand-pin {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: #fff;
        text-align: center;
        line-height: 30px;
        font-size: 14px;
    }
</style>

<div id="map" style="width: 100%; height: 500px;" class="mb-4"></div>

<div class="mb-3">
    <form method="GET" action="/competitor-stores" class="form-inline">
        <label class="mr-2">Años:</label>
        <div class="form-group mr-3">
            @foreach($years as $y)
                <label class="mr-2">
                    <input type="checkbox" name="years[]" value="{{$y}}" @if(is_array(request('years')) && in_array($y, request('years'))) checked @endif> {{$y}}
                </label>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <button type="button" id="download-map" class="btn btn-secondary ml-2">Descargar mapa</button>
    </form>
</div>

<a href="/competitor-stores/create">Crear tienda</a>
<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Latitud</th>
            <th>Longitud</th>
            <th>Franquicia</th>
            <th>Color</th>
            <th>Año de apertura</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($model as $item)
        <tr>
            <td>{{$item->name}}</td>
            <td>{{$item->address}}</td>
            <td>{{$item->latitude}}</td>
            <td>{{$item->longitude}}</td>
            <td>@if(isset($item->franchise)){{$item->franchise->name}}@endif</td>
            <td>
                @if(isset($item->franchise))
                    <span style="display:inline-block;width:20px;height:20px;background:{{$item->franchise->color}};"></span>
                    {{$item->franchise->color}}
                @endif
            </td>
            <td>{{$item->opened_year}}</td>
            <td>
                <a href="/competitor-stores/{{$item->id}}/edit">Editar</a>
                <a href="/competitor-stores/{{$item->id}}/destroy" onclick="return confirm('¿Desea eliminar?')">Eliminar</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    var map = L.map('map').setView([5.0673, -75.4839], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    function getBrand(store) {
        if (store.franchise && store.franchise.name) {
            return store.franchise.name;
        }
        return store.name.split(' ')[0];
    }


    function getColor(store) {
        if (store.franchise && store.franchise.color) {
            return store.franchise.color;
        }
        return '#3388ff';
    }

    var stores = @json($model);
    stores.forEach(function(store){
        if(store.latitude && store.longitude){
            var brand = getBrand(store);

            var color = getColor(store);

            var icon = L.divIcon({
                className: 'custom-div-icon',
                html: '<div class="brand-pin" style="background:' + color + ';">' + brand.charAt(0) + '</div>',
                iconSize: [30, 42],
                iconAnchor: [15, 42]
            });

            L.marker([store.latitude, store.longitude], {icon: icon}).addTo(map)
                .bindPopup('<b>'+store.name+'</b><br>'+ (store.address || ''));
        }
    });

    document.getElementById('download-map').addEventListener('click', function(){
        html2canvas(document.getElementById('map')).then(function(canvas){
            var link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'mapa.png';
            link.click();
        });
    });
</script>
@endsection
