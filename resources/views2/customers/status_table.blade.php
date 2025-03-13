<div id="statusSidebar">
    
    <span id="closeStatusSidebar">✖</span>
            
    <div class="box2">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Estado</th>
                <th scope="col">Descripción</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($statuses_options as $item)
                <tr>
                    <th scope="row">{{$item->name}}</th>
                    <td scope="row">{{$item->description}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
      
</div>

<style>
    /* Estilos del statusSidebar */
    #statusSidebar {
        position: fixed;
        top: 0;
        right: -300px;
        width: 300px;
        height: 100%;
        background: #f8f9fa;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
        transition: right 0.3s ease-in-out;
        overflow-y: auto;
        padding: 20px;
        z-index: 100000;
    }
    #statusSidebar.active {
        right: 0;
        color:black!important;
    }
    #closeStatusSidebar {
        cursor: pointer;
        color: red;
        font-size: 18px;
        font-weight: bold;
        float: right;
    }
    .box {
        display: none;
    }
</style>

<script>
  $(document).ready(function(){
    $("#helpButtonStatus").click(function(){
      $("#statusSidebar").addClass("active");
      $(".box").slideDown();
    });

    $("#closeStatusSidebar").click(function(){
      $(".box").slideUp();
      $("#statusSidebar").removeClass("active");
    });
  });
</script>