<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/checklists/data/CheckListsData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['solid']) || empty($_POST['plaid']) || empty($_POST['fecha'])){throw new Exception("La información esta incompleta.");}

        $checklist=array();
        $solicitud=array();

        $solicitud=FnBuscarSolicitud($conmy, $_POST['solid'], $_SESSION['gesman']['CliId']);
        if(empty($solicitud['id'])){throw new Exception("No se encontró la Solicitud.");}

        $checklist['cliid']=$_SESSION['gesman']['CliId'];
        $checklist['solid']=$solicitud['id'];
        $checklist['plaid']=$_POST['plaid'];
        $checklist['equid']=$solicitud['equid'];
        $checklist['fecha']=$_POST['fecha'];
        $checklist['cliruc']=$solicitud['cliruc'];
        $checklist['clinombre']=$solicitud['clinombre'];
        $checklist['clidireccion']=$solicitud['clidireccion'];
        $checklist['clicontacto']=$solicitud['clicontacto'];
        $checklist['clitelefono']=$solicitud['clitelefono'];
        $checklist['clicorreo']=$solicitud['clicorreo'];
        $checklist['supervisor']=$_SESSION['gesman']['Alias'];
        $checklist['equcodigo']=$solicitud['equcodigo'];
        $checklist['equnombre']=$solicitud['equnombre'];
        $checklist['equmarca']=$solicitud['equmarca'];
        $checklist['equmodelo']=$solicitud['equmodelo'];
        $checklist['equplaca']=$solicitud['equplaca'];
        $checklist['equserie']=$solicitud['equserie'];
        $checklist['equmotor']=$solicitud['equmotor'];
        $checklist['equtransmision']=$solicitud['equtransmision'];
        $checklist['equdiferencial']=$solicitud['equdiferencial'];
        $checklist['equkm']=$solicitud['equkm'];
        $checklist['equhm']=$solicitud['equhm'];
        $checklist['usuario']=date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')';

        $id=FnAgregarCheckList($conmy, $checklist);
        if(empty($id)){throw new Exception("Error generando el CheckList.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se generó el CheckList.';

        $conmy=null;
    } catch(PDOException $ex){
        $datos['msg']=$ex->getMessage();
        $conmy=null;
    } catch (Exception $ex) {
        $datos['msg']=$ex->getMessage();
        $conmy=null;
    }

    echo json_encode($datos);
?>