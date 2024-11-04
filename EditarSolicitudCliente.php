<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";

    if(!FnValidarSesion()){
        header("location:/gesman/Salir.php");
        exit();
    }

    if(!FnValidarSesionManNivel2()){
        header("HTTP/1.1 403 Forbidden");
        exit();
    }

    if(empty($_GET['id'])){
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    $ESTADO=0;
    $SOLICITUD=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $SOLICITUD=FnBuscarSolicitud($conmy, $_GET['id'], $_SESSION['gesman']['CliId']);
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
                <p class="m-0 p-0"><?php echo $_SESSION['gesman']['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $_GET['id'];?>">
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($SOLICITUD['nombre'])?null:$SOLICITUD['nombre'];?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item fw-bold"><a href="/solicitudes/EditarSolicitud.php?id=<?php echo $_GET['id'];?>" class="text-decoration-none">SOLICITUD</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">CLIENTE</li>
                        <li class="breadcrumb-item fw-bold"><a href="/solicitudes/EditarSolicitudEquipo.php?id=<?php echo $_GET['id'];?>" class="text-decoration-none">EQUIPO</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-12 col-sm-8 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Nombre:</p>
                <input type="text" class="form-control" value="<?php if(!empty($SOLICITUD['clinombre'])){echo $SOLICITUD['clinombre'];};?>" readonly/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">RUC</p> 
                <input type="text" class="form-control" value="<?php if(!empty($SOLICITUD['cliruc'])){echo $SOLICITUD['cliruc'];};?>" readonly/>
            </div>
            <div class="col-12 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Dirección</p> 
                <input type="text" id="txtCliDireccion" class="form-control" value="<?php if(!empty($SOLICITUD['clidireccion'])){echo $SOLICITUD['clidireccion'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Contacto</p> 
                <input type="text" id="txtCliContacto" class="form-control" value="<?php if(!empty($SOLICITUD['clicontacto'])){echo $SOLICITUD['clicontacto'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Teléfono</p> 
                <input type="text" id="txtCliTelefono" class="form-control" value="<?php if(!empty($SOLICITUD['clitelefono'])){echo $SOLICITUD['clitelefono'];};?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Correo</p> 
                <input type="text" id="txtCliCorreo" class="form-control" value="<?php if(!empty($SOLICITUD['clicorreo'])){echo $SOLICITUD['clicorreo'];};?>"/>
            </div>
        </div>

        <?php
            if($ESTADO==2){
                echo '
                <div class="row mb-3">
                    <div class="col-12 mb-3">
                        <button type="button" class="btn btn-outline-primary form-control" onclick="FnModificarSolicitudCliente(); return false;"><i class="fas fa-save"></i> Guardar</button>
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
    <script src="/solicitudes/js/EditarSolicitudCliente.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>