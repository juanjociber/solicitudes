<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    $datos = array('res'=>false,'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
        if (empty($_POST['id']) || empty($_POST['actividades'])) {throw new Exception("La información está incompleta.");}

        $solicitud = array();
        $solicitud['Id']=$_POST['id'];
        $solicitud['CliId']=$_SESSION['CliId'];
        $solicitud['Actividades']=$_POST['actividades'];
        $solicitud['Observaciones']=empty($_POST['observaciones'])?null:$_POST['observaciones'];
        $solicitud['Actualizacion']=date('Ymd-His').' ('.$_SESSION['UserName'].')';

        if (FnModificarSolicitud($conmy, $solicitud)) {
            $datos['res'] = true;
            $datos['msg'] = "Se modificó la Solicitud.";
        } else {
            $datos['msg'] = "No se pudo modififar la Solicitud.";
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