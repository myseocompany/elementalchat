@extends('layout')


<?php  function clearWP($str){
  $str = trim($str);
  $str = preg_replace('/\s+/', '', $str);
  
  $str = str_replace("+", "", $str );
  if(strlen($str)>10)
    return $str;
  elseif( strlen($str) == 10 )
    return "57".$str;  
} ?>

@section('content')
<h1>@if(isset($phase)){{$phase->name}}@else Clientes @endif</h1>
<style>
  a:hover {
    color: #4178be;
}
</style>

<!-- Incio tareas pendientes -->
{{-- @include('customers.actions') --}}
<!-- Find tareas pendientes -->


<?php 
  function requestToStr($request){
    $str = "?";
    $url = $request->fullUrl();
    $parsedUrl = parse_url($url);
    
    if(isset($parsedUrl['query'] ))
      $str .= $parsedUrl['query']; 

    return $str; 
  }
 ?>

  <div>
    <a style="color: #4178be;" href="/customers/create">Crear
        <i class="fa fa-plus" aria-hidden="true"></i>
  </a> 
  | <a href="/leads/whatsapp">WhatsApp</a> 
  | <a href="/leads/daily_birthday{{ requestToStr($request) }}">Cumpleaños del dia</a> 
  | <a href="/leads/monthly_birthday">Cumpleaños del mes</a> 
  | <a href="/leads/excel{{ requestToStr($request) }}">Excel</a> 
  | <a href="/customer_unsubscribes">Desuscribir</a>
  | <a href="#" data-toggle="modal" data-target="#miModal">Cambiar Estado</a> 
</div>


<!-- Modal -->
<form action="/leads/change_status{{ requestToStr($request) }}" type="GET" id="changeStatusForm">
  <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Cambiar usuarios de estado</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        @foreach($request->query() as $key => $value)
      <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
          
          <select name="modal_status_id"  id="modal_status_id" class="form-control">
           
            <option value="">Estado...</option>
            @foreach($customer_options->all() as $item)
              <option value="{{$item->id}}" @if ($request->status_id == $item->id) selected="selected" @endif>
                 {{ $item->name }}
                
              </option>
            @endforeach
          </select>
      
         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" id="saveChanges">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>
  </form>
  <script>
    document.getElementById('saveChanges').addEventListener('click', function() {
      var statusSelect = document.getElementById('modal_status_id');
      if (statusSelect.value === '') {
        alert('Por favor, seleccione un estado.');
        return false;
      }else{
        document.getElementById('changeStatusForm').submit();  
      }
      
    });
    </script>
  <!-- fin del modal -->
  
  <br>
{{-- obteber datos del tiempo --}}



  <div>
    @include('customers.filter')
  </div>

<div>
  
  @if($customersGroup->count()>-1)

  
  <ul class="groupbar bb_hbox" id="dashboard">
  
    @foreach($customersGroup as $item)
    <li class="groupBarGroup" style="background-color: {{$item->status_color}}; width: <?php 
        if($customersGroup->count()!=0){
          echo 100/$customersGroup->count();
        }
     ?>%">
      <h3>{{$item->count}}</h3>
     
      <div><a href="#" onclick="changeStatus({{$item->id}})">{{$item->status_name}}</a></div>
    </li>          
    @endforeach
  </ul>

  <style>
      @media screen and (max-width: 992px) {
        #dashboard {
          display: none;
        }
      }

      /* On screens that are 600px wide or less, the background color is olive */
      @media screen and (max-width: 600px) {
        #dashboard {
          display: none;
        }
      }
  </style>
  

  @else
    Sin Estados
  @endif
</div>

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

  {{-- tabla resumen --}}
  <br>
  Registro <strong>{{ $model->currentPage() * $model->perPage() - ( $model->perPage() - 1 ) }}</strong> 
  a <strong>{{ $model->count() + ($model->currentPage() - 1) * $model->perPage() }}</strong> 
  de <strong>{{ $model->total() }}</strong>
  <br>
  
{{-- <div>{{$model->total()}} Registro(s)</div> --}}
<br>
<!-- Prueba boton metodo zero actions -->

<!-- <a href="/emails/zeroActions">cero actions</a> -->

<!-- Fin Prueba boton metodo zero actions -->

  <div class="">
            @if (count($model) > 0)
            <table class="table table-striped table-sm">
              
              </thead>
              <?php $lastStatus=-1 ?>
              <tbody>
               <?php $count=1; ?>
                @foreach($model as $item)
                
                
                {{-- colores --}}
		 		 
                {{-- fin colores --}}
                <!--<tr @if(Auth::user()->role_id == 1) onmouseover="showEditIcon({{$item->id}});" onmouseout="hideEditIcon({{$item->id}});" @endif>-->
                <tr>
                
                  <!--- Fecha del pedido -->
                  <td>
                  <div class="customer_created">

                    @if(isset( $item->name )) 
                      <h5>{{ $item->id }} - {{ $item->name }}</h5>
                    @endif
                   <div> <small><i><a href="/customers/{{ $item->id }}/show">{{$item->created_at}}</a></i></small></div>
                    @if(isset( $item->document )) 
                      <a href="/customers/{{ $item->id }}/show">{{$item->document}}</a>
                    @endif
                    
                    @if(isset( $item->phone )) 
                      <div><a href="/customers/{{ $item->id }}/show">{{ $item->phone }}</a></div>
                    @endif
                    @if(isset( $item->phone2 )) 
                      <div><a href="/customers/{{ $item->id }}/show">{{ $item->phone2 }}</a></div>
                    @endif
                    <div>WP: {{$item->getPhoneWP()}}</div>
                    @if(isset( $item->email )) 
                      <div><a href="/customers/{{ $item->id }}/show">{{ $item->email }}</a></div>
                    @endif
                    @if(isset( $item->address )) 
                      {{$item->address}}
                    <br>
                    @endif
                    @if(isset($item->orders) && (count($item->orders)>0) )
                      Ordenes: 
                      @foreach($item->orders as $item_order)
                      <a href="/orders/{{$item_order->id}}/show">{{$item_order->id}}</a>, 
                      @endforeach
                    @endif
                  
                  {{$item->business}}
                    @if(Auth::user()->role_id == 1)
                      <br>
                      {{$item->ad_name}}
                      <br>
                      {{$item->adset_name}}
                      <br>
                      {{$item->campaign_name}}
                    @endif
                  </div>
                  {{$item->bought_products}}
                  @if(is_numeric($item->total_sold)) $ {{number_format($item->total_sold,0,",",".")}} @endif 
                
                </td>
                
                
                
                  
                
                
                 
                
                 
                   
                
                 
                
                 
                 
                <td class="actions">
                   <!-- Link whatsapp -->
                   <a href="https://wa.me/{{ clearWP($item->phone) }}" target="_blank">

                                      <img src="/img/IconoWA.png" width="30">
                                    </a> 

                 

                 @if (Auth::user()->role_id == 1 )
                   
                    <a href="/customers/{{ $item->id }}/destroy">
                      <span class="btn btn-sm btn-danger fa fa-trash-o" aria-hidden="true" title="Eliminar"></span>
                    </a>
                    @if(isset( $item->document )) 
                    <a href="/optimize/customers/consolidateDuplicates?document={{ $item->document }}">
    
        <span class="btn btn-sm btn-warning fa fa-user-times"  aria-hidden="true" title="Consolidar"></span>  
    
</a>
                    @endif
                 @endif
                 
                 </td>
                 <td class="status-badge">
                    {{-- @if (isset($item->status_id)) {{ substr($item->statuses_name,0,10) }} @endif --}}
                    
                    @if(isset($item->status_id)&&($item->status_id!="")&&(!is_null($item->status)))

                      <div class="item-media">
                        <div class="no-img">
                          <span class="badge" id="customer_status_{{$item->id}}" onclick="openStatuses();" style="background-color:{{$item->status->color}}">           
                          {{substr($item->status->name,0,3)}}
                          </span>
                        </div>
                      </div>
                    @endif 
                  
                         
                    
                 </td> 
                </tr>
                <?php $count++;
                  $lastStatus = $item->status_id;
                ?>
        @endforeach
                <?php $count--;?>
                
              </tbody>
                 <?php 
          
          
            $count++;
          ?>
            </table>
            
                  @endif
                  {{-- {{$model->links()}} --}}
                  {{ $model->appends(request()->input())->links() }}
                  <div>
             {{--  Registro {{ $model->currentPage()*$model->perPage() - ( $model->perPage() - 1 ) }}  a {{ $model->currentPage()*$model->perPage()}} de {{ $model->total()}} --}}
             
            </div>
          </div>


<style>
  table{
    table-layout: fixed;
  }

  td.status-badge {
      width: 10%;
  }

  td.actions {
    text-align: right;
    width: 10%;
  }

  td.created{
    width: 30%;
  }
</style>
  <script type="text/javascript">

  function showEditIcon(id){
    console.log("show_edit_icon_"+id);
    $("#edit_icon_"+id).css("display", "inline");
    $("#edit_icon_campaings_"+id).css("display", "inline");
  }
  function hideEditIcon(id){
    console.log("hide_edit_icon_"+id);
    $("#edit_icon_"+id).css("display", "none");
    $("#edit_icon_campaings_"+id).css("display", "none");
  }

  function nav(value,id) {
    var message = encodeURI(value);
    if (value != "") { 
      endpoint = '/campaigns/'+id+'/getPhone/setMessage/'+message;
        $.ajax({
            type: 'GET',
            url: endpoint,
            dataType: 'json',
            success: function (data) {
                var phone = data;
                   url = "https://api.whatsapp.com/send/?phone="+phone+"&text="+encodeURI(value);
                   window.open(url,'_blank');
            },
            error: function(data) { 
            }
        });

       }
  }

  function setStatus(id){
    console.log(id);
    var status_id = $("#status_id_"+id).val();
    var parameters = {
        id: id,
        status_id: status_id
    };
    $.ajax({
        data:  parameters,
        url:   '/set-customer-status',
        type:  'get',
        beforeSend: function () {
        },
        success:  function (response) { 
          $("#customer_status_12162").attr('style',  'background-color:'+response.color);
          var short_name = response.name.substring(0, 3);
          $("#customer_status_12162").text(short_name);
        }
    });
  }


      function getMessages(id){
        $("#messages_"+id).empty();
        $("#div_campaign_button_"+id).empty();
        var campaign_id = $("#campaign_id_"+id).val();

          endpoint = '/campaigns/'+campaign_id;
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: endpoint,
            dataType: 'json',
            success: function (data) {
                loadMessages(data, id);
            },
            error: function(data) { 
            }
        });
      }

        function loadMessages(data, id){

          str = '<label for="message_id">Mensajes:</label><br>';
          str += '<select name="message_id_'+id+'" id="message_id_'+id+'" class="custom-select" onchange="loadButton('+id+');">;';
          str += '<option value="">Seleccione un mensaje</option>';
          $.each(data, function(i, obj) {
            str += '<option value="'+obj.text+'" >'+(obj.text).substr(0,20)+'</option>';
          });
          str += '</select>';

          $("#messages_"+id).prepend(str);
        }

      function loadButton(id){
        console.log(getSelectedMessage());
        $("#div_campaign_button_"+id).html('<br><a href="'+getSelectedMessage('@if(isset($item)){{$item->getPhone()}}@endif',id)+')" name="campaign_button" id="campaign_button" class="btn btn-sm btn-primary my-2 my-sm-0" target="_blanck"> Enviar</a>')
      }

      function getSelectedMessage(phone, id){
         var msg = $('select[name="message_id_'+id+'"] option:selected').val();

         var url = "https://api.whatsapp.com/send/?phone="+phone+"&text="+encodeURI(msg);
         return url;
      }
  </script>

@endsection
