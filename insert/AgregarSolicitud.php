<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['equid']) || empty($_POST['actividades'])){throw new Exception("La información esta incompleta.");}

        $solicitud = array();

        $solicitud['fecha']=date('Y-m-d');
        $solicitud['usuario']=date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')';
        $solicitud['supervisor']=$_SESSION['gesman']['Alias'];
        $solicitud['actividades']=$_POST['actividades'];
        $solicitud['observaciones']=empty($_POST['observaciones']) ? null : $_POST['observaciones'];

        $cliente=FnBuscarCliente($conmy, $_SESSION['gesman']['CliId']);
        if(empty($cliente['id'])){ throw new Exception("No se encontró el Cliente."); }

        $solicitud['cliid']=$cliente['id'];
        $solicitud['cliruc']=$cliente['ruc'];
        $solicitud['clinombre']=$cliente['nombre'];
        $solicitud['clidireccion']=$cliente['direccion'];
        $solicitud['clicontacto']=null;
        $solicitud['clitelefono']=null;
        $solicitud['clicorreo']=null;

        $equipo=FnBuscarEquipo($conmy, $_SESSION['gesman']['CliId'], $_POST['equid']);
        if(empty($equipo['id'])){ throw new Exception("No se encontró el Equipo."); }

        $solicitud['equid']=$equipo['id'];
        $solicitud['equnombre']=$equipo['nombre'];
        $solicitud['equmarca']=$equipo['marca'];
        $solicitud['equmodelo']=$equipo['modelo'];
        $solicitud['equplaca']=$equipo['placa'];
        $solicitud['equserie']=$equipo['serie'];
        $solicitud['equkm']=empty($_POST['equkm']) ? 0 : $_POST['equkm'];
        $solicitud['equhm']=empty($_POST['equhm']) ? 0 : $_POST['equhm'];

        $id=FnRegistrarSolicitud($conmy, $solicitud);
        if(empty($id)){throw new Exception("Error generando la Solicitud.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se generó la Solicitud.';

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