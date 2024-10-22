<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    $datos = array('res'=>false,'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
        if (empty($_POST['id'])) {throw new Exception("La información está incompleta.");}

        $solicitud = array();
        $solicitud['Id']=$_POST['id'];
        $solicitud['CliId']=$_SESSION['CliId'];
        $solicitud['CliDireccion']=empty($_POST['clidireccion'])?null:$_POST['clidireccion'];
        $solicitud['CliContacto']=empty($_POST['clicontacto'])?null:$_POST['clicontacto'];
        $solicitud['CliTelefono']=empty($_POST['clitelefono'])?null:$_POST['clitelefono'];
        $solicitud['CliCorreo']=empty($_POST['clicorreo'])?null:$_POST['clicorreo'];
        $solicitud['Actualizacion']=date('Ymd-His').' ('.$_SESSION['UserName'].')';

        if (FnModificarSolicitudCliente($conmy, $solicitud)) {
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