<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/checklists/data/CheckListsData.php";

  $datos = array('res' => false, 'id' => 0, 'msg' => 'Error General.');

  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!FnValidarSesion()) { throw new Exception("Se ha perdido la conexión."); }
    if (!FnValidarSesionManNivel1()) { throw new Exception("Usuario no autorizado."); }
    if (empty($_POST['plaid']) || empty($_POST['fecha'])) { throw new Exception("La información está incompleta"); }
    
    $checklist = array();
    // Convertir 'solid' a entero; si no se envía se asume 0 (independiente)
    $solid = isset($_POST['solid']) ? intval($_POST['solid']) : 0;

    if ($solid > 0) {
      // --- CHECKLIST DEPENDIENTE DE SOLICITUD ---
      $solicitud = FnBuscarSolicitud($conmy, $solid, $_SESSION['gesman']['CliId']);
      if (empty($solicitud['id'])) {
        throw new Exception("No se encontró la Solicitud.");
      }
      $checklist['cliid']        = $_SESSION['gesman']['CliId'];
      $checklist['solid']        = $solicitud['id'];
      $checklist['plaid']        = $_POST['plaid'];
      $checklist['equid']        = $solicitud['equid'];
      $checklist['fecha']        = $_POST['fecha'];
      $checklist['cliruc']       = $solicitud['cliruc'];
      $checklist['clinombre']    = $solicitud['clinombre'];
      $checklist['clidireccion'] = $solicitud['clidireccion'];
      $checklist['clicontacto']  = $solicitud['clicontacto'];
      $checklist['clitelefono']  = $solicitud['clitelefono'];
      $checklist['clicorreo']    = $solicitud['clicorreo'];
      $checklist['supervisor']   = $_SESSION['gesman']['Alias'];
      $checklist['equnombre']    = $solicitud['equnombre'];
      $checklist['equmarca']     = $solicitud['equmarca'];
      $checklist['equmodelo']    = $solicitud['equmodelo'];
      $checklist['equplaca']     = $solicitud['equplaca'];
      $checklist['equserie']     = $solicitud['equserie'];
      $checklist['equkm']        = $solicitud['equkm'];
      $checklist['equhm']        = $solicitud['equhm'];
      $checklist['usuario']      = date('Ymd-His') . ' (' . $_SESSION['gesman']['Nombre'] . ')';
    } else {
      // --- CHECKLIST INDEPENDIENTE (NO DEPENDE DE SOLICITUD) ---
      // Se requieren los datos mínimos del equipo: equid, equkm y equhm.
      if (empty($_POST['equid'])) {
        throw new Exception("La información está incompleta.");
      }
      $checklist['solid']        = 0; // Sin dependencia de solicitud.
      $checklist['plaid']        = $_POST['plaid'];
      $checklist['equid']        = $_POST['equid'];
      $checklist['fecha']        = $_POST['fecha'];
      $checklist['supervisor']   = $_SESSION['gesman']['Alias'];
      
      // DATOS CLIENTE:
      $cliente=FnBuscarCliente($conmy, $_SESSION['gesman']['CliId']);
      if(empty($cliente['id'])){ throw new Exception("No se encontró el Cliente."); }
      $checklist['cliid']        = $cliente['id'];
      $checklist['cliruc']       = $cliente['ruc'];
      $checklist['clinombre']    = $cliente['nombre'];
      $checklist['clidireccion'] = $cliente['direccion'];
      $checklist['clicontacto']  = null;
      $checklist['clitelefono']  = null;
      $checklist['clicorreo']    = null;
      
      // DATOS EQUIPO:
      $equipo=FnBuscarEquipo($conmy, $_SESSION['gesman']['CliId'], $_POST['equid']);
      if(empty($equipo['id'])){ throw new Exception("No se encontró el Equipo."); }

      $checklist['equnombre']    = $equipo['nombre'];
      $checklist['equmarca']     = $equipo['marca'];
      $checklist['equmodelo']    = $equipo['modelo'];
      $checklist['equplaca']     = $equipo['placa'];
      $checklist['equserie']     = $equipo['serie'];
      $checklist['equkm']        = empty($_POST['equkm']) ? 0 : $_POST['equkm'];
      $checklist['equhm']        = empty($_POST['equhm']) ? 0 : $_POST['equhm'];
      $checklist['usuario']      = date('Ymd-His') . ' (' . $_SESSION['gesman']['Nombre'] . ')';
    }
    // Función que llama al procedimiento almacenado
    $id = FnRegistrarCheckList($conmy, $checklist);
    if (empty($id)) {
      throw new Exception("Error generando el CheckList.");
    }
    $datos['id']  = $id;
    $datos['res'] = true;
    $datos['msg'] = 'Se generó el CheckList.';
    $conmy = null;
  } catch (PDOException $ex) {
    $datos['msg'] = $ex->getMessage();
    $conmy = null;
  } catch (Exception $ex) {
    $datos['msg'] = $ex->getMessage();
    $conmy = null;
  }
  echo json_encode($datos);
?>
