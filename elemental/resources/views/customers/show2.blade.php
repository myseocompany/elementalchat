@extends('layout')
<?php  function clearWP($str){
  $str = trim($str);
  $str = str_replace("+", "", $str );
  if(strlen($str)>10)
    return $str;
  elseif( strlen($str) == 10 )
    return "57".$str;  
} ?>
@section('content')
@if($model != null)
<h1 class="title"> {{$model->name}}  </h1>
{{-- Alertas --}}
@if (session('status'))
<div class="alert alert-primary alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
  {!! html_entity_decode(session('status')) !!}
</div>
@endif
@if (session('statusone'))
<div class="alert alert-warning alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
  {!! html_entity_decode(session('statusone')) !!}
</div>
@endif
@if (session('statustwo'))
<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
  {!! html_entity_decode(session('statustwo')) !!}
</div>
@endif
{{-- fin alertas --}}
<div class="card-block">
  <form action="customers/{{$model->id}}/edit">
    {{ csrf_field() }}
    <div class="row">
      <div class="col-md-6">
        <div class="row "><div class="col-md-6  lavel"><span class="lavel"><strong>Nombre:</strong></span></div> <div class="col-md-6">{{$model->name}}</div></div>
        <div class="row "><div class="col-md-6  lavel"><span class="lavel"><strong>Documento:</strong></span></div> <div class="col-md-6 col-sm-6">{{$model->document}}</div></div>
        <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Teléfono:</strong></span></div> <div class="col-md-6 col-sm-6">{{$model->phone}} @if($model->getPhone()!="")
          <a href="https://wa.me/{{clearWP($model->phone)}}" target="_blank">Whatsapp</a>@endif</div></div>    
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Celular:</strong></span></div> <div class="col-md-6">{{$model->phone2}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Correo Electrónico:</strong></span></div> <div class="col-md-6">{{$model->email}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Dirección:</strong></span></div> <div class="col-md-6">{{$model->address}}</div></div>
        </div>

        
        <div class="col-md-6">
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>País:</strong></span></div> <div class="col-md-6">{{$model->country}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Departamento:</strong></span></div> <div class="col-md-6">{{$model->department}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Ciudad:</strong></span></div> <div class="col-md-6">{{$model->city}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Cumpleaños:</strong></span></div> <div class="col-md-6">{{$model->birthday}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Género:</strong></span></div> <div class="col-md-6">@if(isset($model->gender)){{$model->gender->value}}@endif</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Actividad económica:</strong></span></div> <div class="col-md-6">@if(isset($model->economic_activity)){{$model->economic_activity->value}}@endif</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Ingresos en SMMLV:</strong></span></div> <div class="col-md-6">@if(isset($model->income)){{$model->income->value}}@endif</div></div>
          <!--
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Tipo de inversión:</strong></span></div> <div class="col-md-6">@if(isset($model->investment)){{$model->investment->value}}@endif</div></div>

            <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Vive con:</strong></span></div> 
                <div class="col-md-6">
                @if(isset($model->houseMates)) 
                    <ul>
                        @foreach($model->houseMates as $meta)
                            <li>{{$meta->value}}</li>
                        @endforeach
                    </ul>
                @endif
                </div>
            </div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Proveniencia de fondos:</strong></span></div> <div class="col-md-6">
                @if(isset($model->fundingSource)) 
                    <ul>
                        @foreach($model->fundingSource as $meta)
                            <li>{{$meta->value}}</li>
                        @endforeach
                    </ul>
                @endif
            </div></div>
        <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Cierre financiero:</strong></span></div> <div class="col-md-6">
                @if(isset($model->finalFundingSource)) 
                    <ul>
                        @foreach($model->finalFundingSource as $meta)
                            <li>{{$meta->value}}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
      -->
		<div class="row">
			<div class="col-md-6 lavel"><span class="lavel"><strong>Calificación:</strong></span></div> 
			<div class="col-md-6 scoring">
				
            	<div class="stars-outer">
          <div class="stars-inner"></div>

          <script type="text/javascript">
  					ratings = {
  					  scoring : {{$model->scoring}}
  					  
  					};

  					 starTotal = 3;
   
  					 for(rating in ratings) {  
  					    // 2
    					  starPercentage = (ratings[rating] / starTotal) * 100;
    					  
    					  // 3
    					  starPercentageRounded = (Math.round(starPercentage / 10) * 10)+'%';
    					  console.log(starPercentageRounded);

    					  // 4
    					  $('.stars-inner').width(starPercentageRounded); 
  					  }
				  </script>
        </div>   
            </div>
        </div>

          

        </div>
      </div>
      <br>
      <h2 class="title">Contacto</h2>
      <div class="row">
        <div class="col-md-6">
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Nombre:</strong></span></div> <div class="col-md-6">{{$model->contact_name}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Correo Electrónico:</strong></span></div> <div class="col-md-6">{{$model->contact_email}}</div></div>
        </div>

        <div class="col-md-6">
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Teléfono:</strong></span></div> <div class="col-md-6">{{$model->contact_phone2}}</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Parentesco:</strong></span></div> <div class="col-md-6">{{$model->contact_position}}</div></div>
        </div>
      </div>
      <br>
      <h2 class="title">Producto</h2>
      <div class="row">
        <div class="col-md-6">
          
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Proyecto:</strong></span></div> <div class="col-md-6">@if(isset($model->project)){{$model->project->name}}@endif</div></div>
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Producto Adquirido:</strong></span></div> <div class="col-md-6">{{$model->bought_products}}</div></div>
          <!--
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Fecha de Separación:</strong></span></div> <div class="col-md-6">{{$model->first_installment_date}}</div></div>
        -->
        </div>
        <div class="col-md-6">
          <div class="row"><div class="col-md-6 lavel"><span class="lavel"><strong>Valor final:</strong></span></div> <div class="col-md-6"> @if(is_numeric($model->total_sold)) $ {{number_format($model->total_sold,0,",",".")}} @endif 
          </div></div>
        </div>
      </div>
      <br>
      <h2 class="title">Gestión</h2>
      <div class="row">
        <div class="col-md-6">
          <div class="row"><div class="col-md-6 lavel"><strong>Estado:</strong></div> <div class="col-md-6">@if(isset($model->status)&& !is_null($model->status)&&$model->status!=''){{$model->status->name}}@endif
          </div></div>
          <div class="row"><div class="col-md-6 lavel"><strong>Asignado a:</strong></div> <div class="col-md-6">
            @if(isset($model->user)&& !is_null($model->user)&&$model->user!=''){{$model->user->name}} @else Sin asignar @endif
          </div></div>
        </div>
        <div class="col-md-6">
          <div class="row"><div class="col-md-6 lavel"><strong>Fuente:</strong></div> <div class="col-md-6">
            @if(isset($model->source)&& !is_null($model->source)&&$model->source!=''){{$model->source->name}}
            @endif
          </div></div>
        </div>  
      </div>
      <div class="row">
        <div class="col-md-12"><span class="lavel"><strong>Notas:</strong></span></div> <div class="col-md-12">{{$model->notes}}</div>
      </div>

      <br>
      <a href="/customers/{{$model->id}}/edit">
        <span class="btn btn-primary btn-sm" aria-hidden="true">Editar prospecto</span>
      </a>
      @if(is_null($model->user_id) || $model->user_id==0)
      <a href="/customers/{{$model->id}}/assignMe">
        <span class="btn btn-primary btn-sm" aria-hidden="true">Asignarme prospecto</span>
      </a>
      @endif
    </form>
  </div>
  <br>

  @if($actual)
  <div class="accordion" id="pedding-action">
    <div class="card">
      <div class="card-header" id="headingOne">
       <h3>
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
         Envío de correos
       </button>
     </h3>
   </div>
   <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
     <form action="/customers/{{$model->id}}/action/mail" method="POST">
      {{ csrf_field() }}
      <div>
        <select name="email_id" id="email_id">
          <option value="">Seleccione una opción</option>
          @foreach($email_options as $email_option)
          <option value="{{$email_option->id}}">{{$email_option->subject}}</option>
          @endforeach
        </select>
      </div>
      <div>
       <input type="hidden" id="customer_id" name="customer_id" value="{{$model->id}}">
       <input class="btn btn-primary btn-sm" type="submit" value="Enviar correo">
     </div>
   </form>
 </div>
</div>
</div>

<div class="card">
  <div class="card-header" id="headingTwo">
    <h2>
      <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
      Archivos</h2>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">

      <form method="POST" action="/customer_files" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
          <div class="container">
            <div class="row">
              <div class="col">Seleccione el archivo</div>
              <div class="col"><input type="file" class="form-control" id="file" name="file" placeholder="email" ></div>
              <input type="hidden" id="customer_id" name="customer_id" value="{{$model->id}}">
              <div class="col"><input type="submit" class="btn btn-sm btn-primary glyphicon glyphicon-pencil" aria-hidden="true"></div>
            </div>
          </div>


        </div>



      </form>

      <div>
        <div class="table">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>

                <th>Url</th>
                <th>Fecha de Creación</th>

                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($model->customer_files as $file)
              <tr>
                <th>{{$file->id}}</th>

                <th><a href="/public/files/{{$file->customer_id}}/{{$file->url}}">{{$file->url}}</a></th>
                <th>{{$file->created_at}}</th>

                <th>
                  <a class="btn btn-danger btn-sm" href="/customer_files/{{$file->id}}/delete" title="Eliminar">Eliminar</a>
                </th>
              </tr>
              @endforeach                              
            </tbody>
          </table>        

        </div>
      </div>
    </div>

  </div>

</div>
@endif

<h2>Acciones</h2>
<div>
  <form action="/customers/{{$model->id}}/action/store" method="POST">
    {{ csrf_field() }}
    <div>
      @if(isset($pending_action))
      <input type="hidden" name="pending_action_id" id="pending_action_id" value="{{$pending_action->id}}">
      <h3>Acción pendiente: <strong>{{$pending_action->note}}</strong></h3>
      @endif

      <textarea name="note" id="note" cols="100" rows="5" required="required"></textarea>
    </div>
    <div>
      <span>
       <select name="type_id" id="type_id" required>
        <option value="">Seleccione una acción</option>
        @foreach($action_options as $item)
        <option value="{{$item->id}}"  @if(isset($pending_action)&&($item->id==$pending_action->type_id))selected="selected"@endif> {{$item->name}}</option>
        @endforeach
      </select>
      <button class="btn btn-link" type="button" data-toggle="tooltip" data-html="true" data-placement="top" title='<h4>Clic para ver todas las acciones</h4>
      <div class="box">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Acción</th>
              <th scope="col">Intervino cliente</th>
              <th scope="col">Intervino Empresa</th>
              <th scope="col">Nota<th>
            </tr>
          </thead>
          <tbody> 
            <tr>
              <th scope="row">Visita web</th>
              <td>x</td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">Leyó email</th>
              <td>x</td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">Facebook de entrada</th>
              <td>x</td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">Instagram de entrada</th>
              <td>x</td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">WhatsApp de entrada</th>
              <td>x</td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">Llamada de entrada</th>
              <td>x</td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">Intento de llamada</th>
              <td></td>
              <td>x</td>
              <td>Durante la llamada no se lográ dar toda la información. El cliente estaba ocupado. Duración 10 segundos</td>
            </tr>
            <tr>
              <th scope="row">Email de salida</th>
              <td></td>
              <td>x</td>
            </tr>
            <tr>
              <th scope="row">No se pudo enviar mail</th>
              <td></td>
              <td>x</td>
            </tr>
            <tr>
              <th scope="row">Llamada de salida ( efectiva )</th>
              <td></td>
              <td>x</td>
              <td>Se le pudo explicar el producto. Duración 5 minutos</td>
            </tr>
            <tr>
              <th scope="row">Whatsapp de salida</th>
              <td></td>
              <td>x</td>
            </tr>
            <tr>
              <th scope="row">Visita sala</th>
              <td>x</td>
              <td>x</td>
            </tr>
            <tr>
              <th scope="row">Autoactualización</th>
              <td>x</td>
              <td></td>
              <td>Se editó el prospecto por que ingresó nuevamente por algún medio</td>
            </tr>
          </tbody>
        </table>
      </div>'> <i class="fa fa-question-circle question"></i></button>
    </span>
    <span>
      <select name="status_id" id="status_id">
        <option value="">Seleccione un estado</option>
        @foreach($statuses_options as $status_option)
        <option value="{{$status_option->id}}">{{$status_option->name}}</option>
        @endforeach
      </select>
      <button class="btn btn-link" type="button" data-toggle="tooltip" data-html="true" data-placement="top" title='
      <div class="box overflow-auto">
        <table class="table">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Estado</th>
              <th scope="col">Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">Oportunidad</th>
              <td>Insertó en la base de datos - Recién entra a la Base de datos</td>
            </tr>
            <tr>
              <th scope="row">Seguimiento</th>
              <td>Se inició una comunicación ( WP, mail ) y aun no contesta</td>
            </tr>
            <tr>
              <th scope="row">No contesta</th>
              <td>No contesta llamada, incontactable</td>
            </tr>
            <tr>
              <th scope="row">Buscando</th>
              <td>Está revisando otros proyectos, ya compró en otro proyecto.</td>
            </tr>
            <tr>
              <th scope="row">Por visitar</th>
              <td>Queda pendiente a ir a sala de ventas/apto modelo</td>
            </tr>
            <tr>
              <th scope="row">Descartado</th>
              <td>Pide explícitamente no contactarlo</td>
            </tr>
            <tr>
              <th scope="row">Negociación</th>
              <td>Ya pasó por todos los estados de información. Espera de decisión</td>
            </tr>
            <tr>
              <th scope="row">Ganado</th>
              <td>Ya compró en el proyecto</td>
            </tr>
          </tbody>
        </table></div>'><i class="fa fa-question-circle question"></i></button>
    </span>

    <span>      
      <label for="font_customers">Calificación:</label>
      <select name="scoring" id="scoring">
                
        @for ($i=0; $i<=3; $i++)
          <option value="{{$i}}" @if($i==$model->scoring)selected="selected" @endif>{{$i}}</option>
        @endfor
      </select>
    </span>

    </div>
    <div style="margin-bottom:1rem;">
      <label for="example-datetime-local-input" class="col-form-label">Fecha y hora</label>
      <div>
        <input class="form-control" name="due_date" type="datetime-local" value="{{$today}}" id="example-datetime-local-input">
      </div>
    </div>
    <div>
      <input class="btn btn-primary btn-sm" type="submit" value="Enviar acción">
      <input type="hidden" id="customer_id" name="customer_id" value="{{$model->id}}">
    </div>
  </form>
</div>

<!-- <div style="margin:1rem 0">
	<h3>Envío de correos</h3>
	<form action="/customers/{{$model->id}}/action/mail" method="POST">
	{{ csrf_field() }}
	<div>
		<select name="email_id" id="email_id">
              <option value="">Seleccione una opción</option>
              @foreach($email_options as $email_option)
				<option value="{{$email_option->id}}">{{$email_option->subject}}</option>
                @endforeach
		</select>
	</div>
	<div>
		 <input type="hidden" id="customer_id" name="customer_id" value="{{$model->id}}">
		 <input class="btn btn-primary btn-sm" type="submit" value="Enviar correo">
	</div>
	</form>
</div> -->

<table class="table table-striped ">
  <thead>

    <th>Fecha</th>
    <th>Tipo Acción</th>
    <th>Creado por</th>
    
    <th>Descripción</th>
    <th></th>
    
  </thead>
  <tbody>
    @foreach($actions as $item)
    @if($item->status_id==1)

    <tr>
      <td>
        <a href="/actions/{{$item->id}}/show" name="pending_action_id_{{$item->id}}" id="pending_action_id_{{$item->id}}">

          {{$item->created_at}}
        </a>
      </td>
      <td>@if(isset($item->type)&& !is_null($item->type)&& $item->type!=''){{$item->type->name}}@endif</td>
      <td>@if(isset($item->creator)&& !is_null($item->creator)&& $item->creator!=''){{$item->creator->name}}@else Automático @endif</td>
      <td>@if(($item->type_id==2 || $item->type_id==4) && ($item->object_id != null)) {{$item->getEmailSubject()}} @else {{$item->note}}@endif</td>
      <td>

        <a href="/actions/{{$item->id}}/destroy">
          <span class="btn btn-sm btn-danger fa fa-trash-o" aria-hidden="true" title="Eliminar"></span>
        </a>
      </td>

      <td>

        <a href="/actions/{{$item->id}}/destroy">
          <span class="btn btn-sm btn-danger fa fa-check" aria-hidden="true" title="Marcar como completado"></span>
        </a>
      </td>
      
    </tr>
    @endif
    @endforeach
    
  </tbody>
</table>

<h2>Archivos</h2>
<form method="POST" action="/customer_files" enctype="multipart/form-data">
  {{ csrf_field() }}
  <div class="form-group">
    <div class="container">
      <div class="row">
        <div class="col">Seleccione el archivo</div>
        <div class="col"><input type="file" class="form-control" id="file" name="file" placeholder="email" ></div>
        <input type="hidden" id="customer_id" name="customer_id" value="{{$model->id}}">
        <div class="col"><input type="submit" class="btn btn-sm btn-primary glyphicon glyphicon-pencil" aria-hidden="true"></div>
      </div>
    </div>
    

  </div>
  
  
  
</form>

<div>
  <div class="table">
    <table class="table table-striped ">
      <thead>
        <tr>
          <th>#</th>

          <th>Url</th>
          <th>Fecha de Creación</th>

          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($model->customer_files as $file)
        <tr>
          <th>{{$file->id}}</th>

          <th><a href="/public/files/{{$file->customer_id}}/{{$file->url}}">{{$file->url}}</a></th>
          <th>{{$file->created_at}}</th>

          <th>
            <a class="btn btn-danger btn-sm" href="/customer_files/{{$file->id}}/delete" title="Eliminar">Eliminar</a>
          </th>
        </tr>
        @endforeach                              
      </tbody>
    </table>        

  </div>
</div>
<br>

<h2>Historial</h2>
<div class="table-responsive">

  <ul class="list-group">

   <?php $now = \Carbon\Carbon::now();?>                 
   @foreach($histories as $history)
   <?php //dd($histories[1]->user->id);?>
   <li class="list-group-item">Cambio de estado a @if (isset($history->status) && ($history->status != ''))
     <strong>{{$history->status->name}}</strong>, @endif actualizado: {{$history->updated_at}} 
     por @if(isset($history->user) && ($history->user != '') && !is_null($history->user)){{$history->user->name}} @else Automatico @endif


     <span class="badge" style="background-color: @if(isset($history->status) && ($history->status_id != '')) {{$history->status->color}};@else gray @endif">
      <?php
      $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $history->updated_at);
      $years = $end->diffInYears($now);
      $months = $end->diffInMonths($now);
      $days = $end->diffInDays($now);
      $hours = $end->diffInHours($now);

      $minutes = $end->diffInMinutes($now);
      $seconds = $end->diffInSeconds($now);

               // dd($now);
      ?>
      @if($years>0){{ $years }} years
      @else @if ($months>0) {{ $months }} hours 
      @else @if ($days>0) {{ $days }} days 
      @else @if ($hours>0) {{ $hours }} hours 
      @else @if ($hours>0) {{ $hours }} hours 
      @else @if ($minutes>0) {{$minutes}} minutes 
      @else @if ($seconds>0) {{$seconds}} seconds 
      @endif @endif @endif @endif @endif @endif @endif
    </span></li>
    @endforeach
    <li class="list-group-item">Cambio de estado <strong>@if(isset($model->status)&& !is_null($model->status)&&$model->status!=''){{$model->status->name}}@endif</strong>
      <span class="badge" style="background-color: @if(isset($model->status)&& !is_null($model->status)&&$model->status!=''){{$model->status->color}}@else gray @endif;">
      Actual</span></li>
    </ul>
    
  </div>
  @endif
  @endsection
