<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();
if(!isset($_SESSION["nombre"])){

  header("location: login.php");

}else{

 require 'header.php';//requerimos el archivo header.php

if($_SESSION['escritorio']==1){
    require_once "../models/Consultas.php";
    $consultas = new Consultas();
    //TODODatos para mostrar la grafica barras de la compra
    $rspta = $consultas->comprasUltimos_10dias();

    $fechasCompra = '';
    $totalesCompra = '';

    while ($regFechaCompra = $rspta->fetch_object()) {
        $fechasCompra = $fechasCompra."'".$regFechaCompra->fecha."',";
        $totalesCompra = $totalesCompra.$regFechaCompra->total.',';
    }
      
    //Quitamos la ultima coma
    $fechasCompra = substr($fechasCompra, 0 ,-1);
    $totalesCompra = substr($totalesCompra, 0 ,-1);

    //TODODatos para mostrar la grafica barras de la venta
    $rspta = $consultas->ventasUltimos_12meses();
    $fechasVenta = '';
    $totalesVenta = '';

    while ($regMesVenta = $rspta->fetch_object()) {
        $fechasVenta = $fechasVenta."'".$regMesVenta->fecha."',";
        $totalesVenta = $totalesVenta.$regMesVenta->total.',';
    }      
    //Quitamos la ultima coma
    $fechasVenta = substr($fechasVenta, 0 ,-1);
    $totalesCompra = substr($totalesCompra, 0 ,-1);
?>

<!--Contenido-->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        
        <!-- Main content -->
        <section class="content">
            <div class="row">
              <div class="col-md-12">
                  <div class="box">
                    <div class="box-header with-border">
                          <h1 class="box-title">Escritorio </h1>
                    </div>

                    <!-- /.box-header --> 
                    <!-- centro -->
                    <div class="panel-body" >
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h4 style="font-size:17px;">
                                    <strong id="total_compra"></strong>
                                    </h4>
                                    <p>Compras</p>
                                </div>                                
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>                         
                                <a href="ingreso.php" class="small-box-footer">Compras <i class="fa fa-arrow-circle-right"></i></a>       
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h4 style="font-size:17px;">
                                    <strong id="total_venta"></strong>
                                    </h4>
                                    <p>Ventas</p>
                                </div>                                
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>                         
                                <a href="venta.php" class="small-box-footer">Ventas <i class="fa fa-arrow-circle-right"></i></a>       
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    Compras de los últimos 10 días.
                                </div>
                                <div class="box-body">
                                    <canvas id="compras" width="400" heigth="300"></canvas>
                                </div>
                            </div>    
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="box box-primary">
                                <div class="box-header with-border">
                                    Ventas de los últimos 12 meses.
                                </div>
                                <div class="box-body">
                                    <canvas id="ventas" width="400" heigth="300"></canvas>
                                </div>
                            </div>    
                        </div>                                                    
                    </div>
                    
                    <!--Fin centro -->
                  </div><!-- /.box -->
              </div><!-- /.col -->
          </div><!-- /.row -->
      </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
  <!--Fin-Contenido-->

<?php
}else{
  require 'accesoDenegado.php';
}

 require 'footer.php';//requerimos el archivo footer.php
?>
<script type="text/javascript" src="../public/js/Chart.min.js"></script>
<script type="text/javascript" src="../public/js/Chart.bundle.min.js"></script>
<script type="text/javascript" src="scripts/totalcompra-ventahoy.js "></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" >
    //TODO:Compras
    const ctxCompras = document.getElementById('compras');

    new Chart(ctxCompras, {
        type: 'bar',
        data: {
        labels: [<?php echo $fechasCompra;?>],
        datasets: [{
            label: '# Compras en Bs. de los últimos 10 días',
            data: [<?php echo $totalesCompra;?>],
            borderWidth: 1
        }]
        },
        options: {
        scales: {
            y: {
            beginAtZero: true
            }
        }
        }
    });

    //TODO:Ventas
    const ctxVentas = document.getElementById('ventas');

    new Chart(ctxVentas, {
        type: 'bar',
        data: {
        labels: [<?php echo $fechasVenta;?>],
        datasets: [{
            label: '# Ventas en Bs. de los últimos 12 meses',
            data: [<?php echo $totalesVenta;?>],
            borderWidth: 1
        }]
        },
        options: {
        scales: {
            y: {
            beginAtZero: true
            }
        }
        }
    });
</script>

<?php
}
//Liberar el espacio almacenado en buffer
ob_end_flush();
?>
