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
        <label>Dirección:</label>
        <input type="text" name="address" class="form-control" value="{{$model->address}}">
    </div>
    <div class="form-group">
        <label>Latitud:</label>
        <input id="latitude" type="text" name="latitude" class="form-control" value="{{$model->latitude}}">
    </div>
    <div class="form-group">
        <label>Longitud:</label>
        <input id="longitude" type="text" name="longitude" class="form-control" value="{{$model->longitude}}">
    </div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        #map { width: 100%; height: 300px; }
    </style>
    <div id="map" class="mb-3"></div>
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

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var lat = parseFloat(latInput.value) || 5.0673;
    var lng = parseFloat(lngInput.value) || -75.4839;

    var map = L.map('map').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        crossOrigin: true
    }).addTo(map);

    var marker = L.marker([lat, lng]).addTo(map);

    function updateMarker() {
        var lat = parseFloat(latInput.value);
        var lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        }
    }

    latInput.addEventListener('input', updateMarker);
    lngInput.addEventListener('input', updateMarker);
</script>
@endsection
