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
        $solicitud['EquNombre']=empty($_POST['equnombre'])?null:$_POST['equnombre'];
        $solicitud['EquMarca']=empty($_POST['equmarca'])?null:$_POST['equmarca'];
        $solicitud['EquModelo']=empty($_POST['equmodelo'])?null:$_POST['equmodelo'];
        $solicitud['EquPlaca']=empty($_POST['equplaca'])?null:$_POST['equplaca'];
        $solicitud['EquSerie']=empty($_POST['equserie'])?null:$_POST['equserie'];
        $solicitud['EquMotor']=empty($_POST['equmotor'])?null:$_POST['equmotor'];
        $solicitud['EquTransmision']=empty($_POST['equtransmision'])?null:$_POST['equtransmision'];
        $solicitud['EquDiferencial']=empty($_POST['equdiferencial'])?null:$_POST['equdiferencial'];
        $solicitud['EquKm']=empty($_POST['equkm'])?0:$_POST['equkm'];
        $solicitud['EquHm']=empty($_POST['equhm'])?0:$_POST['equhm'];
        $solicitud['Actualizacion']=date('Ymd-His').' ('.$_SESSION['UserName'].')';

        if (FnModificarSolicitudEquipo($conmy, $solicitud)) {
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