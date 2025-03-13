@extends('layout')

@section('content')
<h1>Crear Cliente</h1>
{{-- FORMULARIO CLIENTES --}}
<form method="POST" action="/customers">
{{ csrf_field() }}

<fieldset class="scheduler-border">
  <legend class="scheduler-border">Datos Personales:</legend>
    <div class="row">
      <div class="col-sm-6">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label for="name">Nombre:</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Nombre..." value="">
            </div>
          </div>

          <div class="col-sm-12">
            <div class="form-group">
              <label for="document">CC:</label>
              <input type="text" class="form-control" id="document" name="document" placeholder="Doc Ej: C.c/DNI...">
            </div>
          </div>

          <div class="col-sm-12">
           <div class="form-group">
            <label for="phone">Cel:</label>
            <input type="number" class="form-control" id="phone" name="phone" placeholder="Celular...">
          </div>
        </div>

          <div class="col-sm-12">
            <div class="form-group">
              <label for="email">Correo:</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Correo Electronico...">
            </div>
          </div>

          <div class="col-sm-12">
            <div class="form-group">
              <label for="address">Dirección:</label>
              <input type="text" class="form-control" id="address" name="address" placeholder="Dirección...">
            </div>
          </div>



        </div>
      </div>
      <div class="col-sm-6">
      <div class="col-sm-12">
          <div class="form-group">
            <label for="document">Quien formula:</label>
            <input type="text" class="form-control" id="refered_by" name="refered_by" placeholder="Quien formula" >
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            <label for="document">F. de cumpleaños:</label>
            <input type="date" class="form-control" id="birthday" name="birthday" >
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            <label for="document">Medio de pago:</label>
            <input type="text" class="form-control" id="payment" name="payment"  placeholder="Medio de pago">
          </div>
        </div>
      </div>
        

        

    </div>
</fieldset>
<fieldset class="scheduler-border">
  <legend class="scheduler-border">Orden:</legend>
  {{-- Estado --}}

  <div class="form-group">
    <label for="bought_products">Productos en la orden:</label>
    <textarea class="form-control" name="bought_products" id="bought_products" cols="30" rows="10" placeholder="Producto"></textarea>
    
  </div>
  <div class="">
    <label for="total_sold">Total:</label>
    <input type="number" name="total_sold" id="total_sold" placeholder="" class="form-control" placeholder="total">
  </div>
</fieldset>

  {{-- Fin datos de contacto --}}
  <fieldset class="scheduler-border">
    <legend class="scheduler-border">Datos Adicionales:</legend>
    {{-- Estado --}}

  
  <!--
  <div class="form-group">
    <label for="first_installment_date">Fecha de Separación:</label>
    <input type="date" class="form-control" name="first_installment_date" id="first_installment_date">
  </div>
  -->
   {{-- Asignado a --}}
  <div class="form-group">
    <label for="users">Asignado A:</label>
    <select name="user_id" id="user_id" class="form-control">
      <option value="">Seleccione...</option>
      @foreach ($users as $item)
      <option value="{{ $item->id }}">{{  $item->name }}</option>
      @endforeach
    </select>
  </div>
  {{-- fuente --}}
  <div class="">
    <label for="pathology">Patología:</label>
    <textarea name="pathology" id="pathology" placeholder="" cols="30" rows="5" class="form-control"></textarea>
  </div>
  <div class="">
    <label for="hobbie">Pasatiempos:</label>
    <textarea name="hobbie" id="hobbie" placeholder="" cols="30" rows="5" class="form-control"></textarea>
  </div>
  <div class="">
    <label for="notes">Notas:</label>
    <textarea name="notes" id="notes" placeholder="" cols="30" rows="5" class="form-control"></textarea>
  </div>
</fieldset>

<fieldset class="scheduler-border">
  <legend class="scheduler-border">Datos Personales:</legend>
  <div class="row">
        
        
        
        <div class="col-sm-3">
          <div class="form-group">
            <label for="phone2">Teléfono:</label>
            <input type="number" class="form-control" id="phone2" name="phone2" placeholder="Telefono...">
          </div>
        </div>
         
        
        <div class="col-sm-3">
          <div class="form-group">
            <label for="country">País:</label>
            <input type="text" class="form-control" id="country" name="country" placeholder="País...">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label for="department">Departamento:</label>
            <input type="text" class="form-control" id="department" name="department" placeholder="Departamento...">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label for="city">Ciudad:</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="Ciudad...">
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            <label for="business">Empresa:</label>
            <input type="text" class="form-control" id="business" name="business" placeholder="Empresa...">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label for="position">Cargo:</label>
            <input type="text" class="form-control" id="position" name="position" placeholder="Cargo..">
          </div>    
        </div>
        
        
  </div>
  <div class="row">
  <div class="col-sm-3">
          <div class="form-group">
            <label for="document">Genero:</label>
            <select id="meta_gender_id" name="meta_gender_id" class="form-control">
              <option>Seleccione...</option>
              @foreach(App\CustomerMetaData::getOptions(1) as $item)
              <option value="{{$item->id}}">{{$item->value}}</option>
              @endforeach
            </select>
          </div>
        </div>
    <div class="col-sm-3">
    <div class="form-group">
      <label for="customers_statuses">Estado:</label>
      <select name="status_id" id="status_id" class="form-control" multiple>
        <option value="">Seleccione...</option>
        @foreach ($customers_statuses as $item)
        <option value="{{ $item->id }}">{{  $item->name }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="form-group">
      <label for="font_customers">Proyecto:</label>
      <select name="project_id" id="project_id" class="form-control">
        <option value="">Seleccione...</option>
        @foreach ($projects as $item)
        <option value="{{ $item->id }}">{{  $item->name }}</option>
        @endforeach
      </select>
    </div>
  </div>
  
  <div class="col-sm-3">
    <div class="form-group">
      <label for="font_customers">Fuente:</label>
      <select name="source_id" id="source_id" class="form-control">
        <option value="">Seleccione...</option>
        @foreach ($customer_sources as $item)
        <option value="{{ $item->id }}">{{  $item->name }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="form-group">
      <label for="font_customers">Calificación:</label>
      <select name="scoring" id="scoring" class="form-control">
        @for ($i=0; $i<=5 ; $i++)
          <option value="{{$i}}">{{$i}}</option>
        @endfor
      </select>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="form-group">
      <label for="instagram_url">Instagram:</label>
      <input type="text" class="form-control" id="instagram_url" name="instagram_url" value="" placeholder="Instagram URL">
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group">
      <label for="facebook_url">Facebook:</label>
      <input type="text" class="form-control" id="facebook_url" name="facebook_url" value="" placeholder="Facebook URL">
    </div>
  </div>
    
  </div>
  </fieldset>
  {{-- Datos de contacto --}}
  <fieldset class="scheduler-border">
  <legend class="scheduler-border">Contactos adicionales:</legend>
  <div class="row">
        <div class="col-sm-3">
        <div class="form-group">
          <label for="contact_name">Nombre:</label>
          <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Nombre del contacto" value="">
        </div>
        </div>
        <div class="col-sm-3">
           <div class="form-group">
            <label for="contact_phone2">Celular:</label>
            <input type="text" class="form-control" id="contact_phone2" name="contact_phone2" placeholder="Celular...">
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="form-group">
            <label for="contact_email">Correo Electrónico:</label>
            <input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="Correo Electronico...">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label for="contact_position">Cargo:</label>
            <input type="text" class="form-control" id="contact_position" name="contact_position" placeholder="Cargo..">
          </div>    
        </div>
        
  </div>
  </fieldset>
  <button type="submit" class="btn btn-primary">Enviar</button>
</form>

@endsection
