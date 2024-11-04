<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    $datos = array('res'=>false,'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if (empty($_POST['id'])) {throw new Exception("La información está incompleta.");}

        $solicitud=array();
        $solicitud['Id']=$_POST['id'];
        $solicitud['CliId']=$_SESSION['gesman']['CliId'];
        $solicitud['Usuario']=date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')';

        if (FnFinalizarSolicitud($conmy, $solicitud)) {
            $datos['res'] = true;
            $datos['msg'] = "Se finalizó la Solicitud.";
        } else {
            $datos['msg'] = "No se pudo finalizar la Solicitud.";
        }
        $conmy = null;
    } catch (PDOException $ex) {
        $conmy = null;
        $datos['msg'] = $ex->getMessage();
    } catch (Exception $ex) {
        $conmy = null;
        $datos['msg'] = $ex->getMessage();
    }

    echo json_encode($datos);
?>