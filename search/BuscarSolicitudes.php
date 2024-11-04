<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    $datos = array('data'=>array(), 'res'=>false, 'pag'=>0, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['fechainicial']) || empty($_POST['fechafinal'])) {throw new Exception("Las fechas de busqueda están incompletas.");}

        $search=array(
            'cliid'=>$_SESSION['gesman']['CliId'],
            'equid'=>empty($_POST['equipo']) ? 0 : $_POST['equipo'],
            'nombre'=>empty($_POST['nombre']) ? '' : $_POST['nombre'],
            'fechainicial'=>$_POST['fechainicial'],
            'fechafinal'=>$_POST['fechafinal'],
            'pagina'=>empty($_POST['pagina']) ? 0 : $_POST['pagina']
        );
        
        $response = FnBuscarSolicitudes($conmy, $search);

        if ($response['pag']>0) {
            $datos['res'] = true;
            $datos['msg'] = 'Ok.';
            $datos['data'] = $response['data'];
            $datos['pag'] = $response['pag'];
        } else {
            $datos['msg'] = 'No se encontró resultados.';
        }
        $conmy = null;
    } catch(PDOException $ex) {
        $datos['msg'] = $ex->getMessage();
        $conmy = null;
    } catch (Exception $ex) {
        $datos['msg'] = $ex->getMessage();
        $conmy = null;
    }

    echo json_encode($datos);

?>