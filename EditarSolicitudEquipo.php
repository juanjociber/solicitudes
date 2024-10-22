<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    $ID=empty($_GET['id']) ? 0 : $_GET['id'];
    $ESTADO=0;
    $SOLICITUD=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $SOLICITUD=FnBuscarSolicitud($conmy, $ID, $_SESSION['CliId']);
        if(!empty($SOLICITUD['estado'])){
            $ESTADO=$SOLICITUD['estado'];
        }
        $conmy==null;
    } catch(PDOException $ex) {
        $conmy = null;
    } catch (Exception $ex) {
        $conmy = null;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Solicitud | GPEM SAC.</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
</head>
<body>

    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>

    <div class="container section-top">
        <div class="row mb-3">
            <div class="col-12 btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarSolicitudes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Solicitudes</span></button>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnSolicitud(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
            </div>
        </div>

        <div class="row border-bottom mb-3 fs-5">
            <div class="col-12 fw-bold" style="display:flex; justify-content:space-between;">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $ID;?>">
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($SOLICITUD['nombre'])?null:$SOLICITUD['nombre'];?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item fw-bold"><a href="/solicitudes/EditarSolicitud.php?id=<?php echo $ID;?>" class="text-decoration-none">SOLICITUD</a></li>
                        <li class="breadcrumb-item fw-bold"><a href="/solicitudes/EditarSolicitudCliente.php?id=<?php echo $ID;?>" class="text-decoration-none">CLIENTE</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">EQUIPO</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Código:</p>
                <input type="text" class="form-control" value="<?php if(!empty($SOLICITUD['equcodigo'])){echo $SOLICITUD['equcodigo'];};?>" readonly/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Nombre</p> 
                <input type="text" id="txtEquNombre" class="form-control" value="<?php if(!empty($SOLICITUD['equnombre'])){echo $SOLICITUD['equnombre'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Placa</p> 
                <input type="text" id="txtEquPlaca" class="form-control" value="<?php if(!empty($SOLICITUD['equplaca'])){echo $SOLICITUD['equplaca'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Marca</p> 
                <input type="text" id="txtEquMarca" class="form-control" value="<?php if(!empty($SOLICITUD['equmarca'])){echo $SOLICITUD['equmarca'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Modelo</p> 
                <input type="text" id="txtEquModelo" class="form-control" value="<?php if(!empty($SOLICITUD['equmodelo'])){echo $SOLICITUD['equmodelo'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Serie</p> 
                <input type="text" id="txtEquSerie" class="form-control" value="<?php if(!empty($SOLICITUD['equserie'])){echo $SOLICITUD['equserie'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Motor</p> 
                <input type="text" id="txtEquMotor" class="form-control" value="<?php if(!empty($SOLICITUD['equmotor'])){echo $SOLICITUD['equmotor'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Diferencial</p> 
                <input type="text" id="txtEquDiferencial" class="form-control" value="<?php if(!empty($SOLICITUD['equdiferencial'])){echo $SOLICITUD['equdiferencial'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Transmisión</p> 
                <input type="text" id="txtEquTransmision" class="form-control" value="<?php if(!empty($SOLICITUD['equtransmision'])){echo $SOLICITUD['equtransmision'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Km</p> 
                <input type="number" id="txtEquKm" class="form-control" value="<?php echo empty($SOLICITUD['equkm']) ? 0 : $SOLICITUD['equkm'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Hm</p> 
                <input type="number" id="txtEquHm" class="form-control" value="<?php echo empty($SOLICITUD['equhm']) ? 0 : $SOLICITUD['equhm'];?>"/>
            </div>
        </div>

        <?php
            if($ESTADO==2){
                echo '
                <div class="row mb-3">
                    <div class="col-12 mb-3">
                        <button type="button" class="btn btn-outline-primary form-control" onclick="FnModificarSolicitudEquipo(); return false;"><i class="fas fa-save"></i> Guardar</button>
                    </div>
                </div>';
            }        
        ?>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/solicitudes/js/EditarSolicitudEquipo.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>