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

        $solicitud['Fecha']=date('Y-m-d');
        $solicitud['Usuario']=date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')';
        $solicitud['Supervisor']=$_SESSION['gesman']['Alias'];
        $solicitud['Actividades']=$_POST['actividades'];
        $solicitud['Observaciones']=empty($_POST['observaciones']) ? null : $_POST['observaciones'];

        $cliente=FnBuscarCliente($conmy, $_SESSION['gesman']['CliId']);
        if(empty($cliente['id'])){ throw new Exception("No se encontró el Cliente."); }

        $solicitud['CliId']=$cliente['id'];
        $solicitud['CliRuc']=$cliente['ruc'];
        $solicitud['CliNombre']=$cliente['nombre'];
        $solicitud['CliDireccion']=$cliente['direccion'];
        $solicitud['CliContacto']=null;
        $solicitud['CliTelefono']=null;
        $solicitud['CliCorreo']=null;

        $equipo=FnBuscarEquipo($conmy, $_SESSION['gesman']['CliId'], $_POST['equid']);
        if(empty($equipo['id'])){ throw new Exception("No se encontró el Equipo."); }

        $solicitud['EquId']=$equipo['id'];
        $solicitud['EquCodigo']=$equipo['codigo'];
        $solicitud['EquNombre']=$equipo['nombre'];
        $solicitud['EquMarca']=$equipo['marca'];
        $solicitud['EquModelo']=$equipo['modelo'];
        $solicitud['EquPlaca']=$equipo['placa'];
        $solicitud['EquSerie']=$equipo['serie'];
        $solicitud['EquMotor']=$equipo['motor'];
        $solicitud['EquDiferencial']=$equipo['diferencial'];
        $solicitud['EquTransmision']=$equipo['transmision'];
        $solicitud['EquKm']=empty($_POST['equkm']) ? 0 : $_POST['equkm'];
        $solicitud['EquHm']=empty($_POST['equhm']) ? 0 : $_POST['equhm'];

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