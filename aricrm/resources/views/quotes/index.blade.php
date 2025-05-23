@extends('layout')

@section('content')
<h1>{{$statuses->name}}</h1>

@include('orders.dashboard')
@include('orders.filter')



<div><a href="/orders/create/sid/{{$request->status_id}}" >Crear</a></div>

<table class="table">
<tr>
	<th></th>
  
  <th>Apartamento</th>
	<th>Parqueadero</th>
  <th>Bodega</th>
  <th>Titular</th>
  <th>Paz y Salvo</th>

  
	
  <th>Acciones</th>
</tr>
<?php $i=1;?> 
@foreach($model as $item)
<tr>
	<td><a href="/orders/{{$item->id}}/show">{{$i++}}</a></td>
  
  <td><a href="/products/{{$item->product_id}}/show">{{$item->name}}</a></td>
  <td>@if(isset($item->parking)){{$item->parking->name}}@endif</td>
  <td>@if(isset($item->deposit)){{$item->deposit->name}}@endif</td>
  
  <td>@if(isset($item->customer))
    <a href="/customers/{{$item->customer_id}}/show">{{$item->customer->name}}</a>
  @endif</td>
  <td>@if(($item->good_standing_certificate==1)){{"OK"}}@endif 
    </td>
  
  <td><a href="/orders/{{$item->id}}/show">Ver</a></td>
  
</tr>
@endforeach
</table>

@endsection