<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
	require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

	$datos=array('res'=>false, 'msg'=>'Error General.', 'data'=>array());

	try{
		$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if(empty($_SESSION['CliId'])){throw new Exception("Se ha perdido la conexión.");}

		$codigo=empty($_POST['codigo']) ? "" : $_POST['codigo'];
		$data=FnListarEquipos($conmy, $_SESSION['CliId'], $codigo);
		
		$datos['res']=true;
		$datos['msg']='Ok.';
		$datos['data']=$data;

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