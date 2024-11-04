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
    $PLANTILLAS=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $SOLICITUD=FnBuscarSolicitud($conmy, $_GET['id'], $_SESSION['gesman']['CliId']);
        $PLANTILLAS=FnListarPlantilla($conmy);
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
    <title>Solicitud de Servicio | GPEM SAC.</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
        .divselect {
            cursor: pointer;
            transition: all .25s ease-in-out;
        }
        .divselect:hover {
            background-color: #ccd1d1 !importart;
            transition: background-color .5s;
        }
    </style>
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
    
    <div class="container section-top">
        <div class="row mb-3 gpem-hide-print">
            <div class="col-12 btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnSolicitudes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Solicitudes</span></button>
                <?php
                    if($ESTADO==2){
                        echo '<button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEditarSolicitud(); return false;"><i class="fas fa-edit"></i><span class="d-none d-sm-block"> Editar</span></button>';
                        echo '<button type="button" class="btn btn-outline-primary fw-bold" onclick="FnModalFinalizarSolicitud(); return false;"><i class="fas fa-check-square"></i><span class="d-none d-sm-block"> Finalizar</span></button>';
                    }
                ?>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnModalAgregarCheckList(); return false;"><i class="fas fa-plus"></i><span class="d-none d-sm-block"> CheckList</span></button>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnModalAgregarOrden(); return false;"><i class="fas fa-plus"></i><span class="d-none d-sm-block"> Orden</span></button>
            </div>
        </div>

        <div class="row border-bottom mb-2 fs-5">
            <div class="col-12 fw-bold d-flex justify-content-between">
                <p class="m-0 p-0"><?php echo $_SESSION['gesman']['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $_GET['id'];?>">
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($SOLICITUD['nombre'])?null:$SOLICITUD['nombre'];?></p>
            </div>
        </div>

        <div class="row p-1 mb-0">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">SOLICITUD</p>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Fecha</p> 
                <p class="m-0 p-0"><?php echo empty($SOLICITUD['fecha'])?'0000/00/00':$SOLICITUD['fecha'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Supervisor</p> 
                <p class="m-0 p-0"><?php echo empty($SOLICITUD['supervisor'])?'UNKNONW':$SOLICITUD['supervisor'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Estado</p>
                <?php
                    switch ($ESTADO){
                        case 1:
                            echo "<span class='badge bg-danger'>Anulado</span>";
                            break;
                        case 2:
                            echo "<span class='badge bg-primary'>Abierto</span>";
                            break;
                        case 3:
                            echo "<span class='badge bg-success'>Cerrado</span>";
                            break;
                        default:
                            echo "<span class='badge bg-secondary'>Unknown</span>";
                    }
                ?>
            </div>
            
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Actividades</p> 
                <p class="m-0 p-0"><?php echo empty($SOLICITUD['actividades'])?'UNKNOWN':$SOLICITUD['actividades'];?></p>
            </div>

            <?php
                if(!empty($SOLICITUD['observaciones'])){
                    echo '
                    <div class="col-12 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Observaciones</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['observaciones'].'</p>
                    </div>';
                }
            ?>
        </div>

        <div class="row p-1 mb-0">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">CLIENTE</p>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Nombre:</p> 
                <p class="m-0 p-0"><?php echo empty($SOLICITUD['clinombre'])?'UNKNOWN':$SOLICITUD['clinombre'];?></p>
            </div>
            <?php
                if(!empty($SOLICITUD['cliruc'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">RUC</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['cliruc'].'</p>
                    </div>';
                }
            ?>

            <?php
                if(!empty($SOLICITUD['clidireccion'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Dirección</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['clidireccion'].'</p>
                    </div>';
                }
            ?>

            <?php
                if(!empty($SOLICITUD['clicontacto'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Contacto</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['clicontacto'].'</p>
                    </div>';
                }
            ?>

            <?php
                if(!empty($SOLICITUD['clitelefono'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Teléfono</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['clitelefono'].'</p>
                    </div>';
                }
            ?>

            <?php
                if(!empty($SOLICITUD['clicorreo'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Correo</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['clicorreo'].'</p>
                    </div>';
                }
            ?>
        </div>

        <div class="row p-1 mb-0">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">EQUIPO</p>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Código</p> 
                <p class="m-0 p-0"><?php echo empty($SOLICITUD['equcodigo'])?'UNKNOWN':$SOLICITUD['equcodigo'];?></p>
            </div>
            <?php
                if(!empty($SOLICITUD['equnombre'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Nombre</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equnombre'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equplaca'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Placa</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equplaca'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equmarca'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Marca</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equmarca'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equmodelo'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Modelo</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equmodelo'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equserie'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Serie</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equserie'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equmotor'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Motor</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equmotor'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equdiferencial'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Diferencial</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equdiferencial'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equtransmision'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Transmisión</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equtransmision'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equkm'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Km.</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equkm'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($SOLICITUD['equhm'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Hm.</p> 
                        <p class="m-0 p-0">'.$SOLICITUD['equhm'].'</p>
                    </div>';
                }
            ?>
        </div>
    </div>

    <div class="modal fade" id="modalFinalizarSolicitud" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">FINALIZAR SOLICITUD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-1">
                    <div class="row text-center fw-bold pt-3">                        
                        <p class="text-center">Para finalizar la Solicitud <?php echo empty($SOLICITUD['nombre'])?'UNKNOWN':$SOLICITUD['nombre'];?> haga clic en el botón CONFIRMAR.</p>                    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnFinalizarSolicitud(); return false;">CONFIRMAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarOrden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">AGREGAR ORDEN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <p class="m-0" style="font-size:12px;">Tipo</p>
                            <select class="form-select" id="cbOrdTipo">
                                <option value="0">Seleccionar</option>
                                <option value="1">CORRECTIVA</option>
                                <option value="2">PREVENTIVA</option>
                                <option value="3">INTERNA</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Orden:</p>
                            <input type="text" class="form-control" id="txtOrdNombre"/>
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Fecha:</p>
                            <input type="date" class="form-control" id="dtpOrdFecha" value="<?php echo date('Y-m-d');?>"/>
                        </div>
                        <div class="col-12">
                            <p class="m-0 text-secondary" style="font-size:13px;">Actividad:</label>
                            <textarea class="form-control" id="txtOrdActividad" rows="2"><?php if(!empty($SOLICITUD['actividades'])){ echo $SOLICITUD['actividades'];}?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAgregarOrden(); return false;">CONFIRMAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarCheckList" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">AGREGAR CHECKLIST</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size:13px;">Plantilla:</label>
                            <select class="form-select" id="cbPlantilla">
                                <option value="0">Seleccionar</option>
                                <?php
                                    foreach($PLANTILLAS as $key=>$valor){
                                        echo '<option value="'.$valor['id'].'">'.$valor['nombre'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size:13px;">Fecha:</label>
                            <input type="date" class="form-control" id="dtpFecha" value="<?php echo date('Y-m-d');?>"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAgregarCheckList(); return false;">CONFIRMAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/jquery-3.5.1/jquery-3.5.1.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/solicitudes/js/Solicitud.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>

</body>
</html>