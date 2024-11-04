<?php 
    function FnRegistrarSolicitud($conmy, $solicitud) {
        try {
            $stmt = $conmy->prepare("CALL spman_agregarsolicitud(:_cliid, :_equid, :_fecha, :_cliruc, :_clinombre, :_clidireccion, :_clicontacto, :_clitelefono, 
            :_clicorreo, :_supervisor, :_equcodigo, :_equnombre, :_equmarca, :_equmodelo, :_equplaca, :_equserie, :_equmotor, :_equtransmision, :_equdiferencial, 
            :_equkm, :_equhm, :_actividades, :_observaciones, :_usuario, @_id)");
            $stmt->bindParam(':_cliid', $solicitud['CliId'], PDO::PARAM_INT);
            $stmt->bindParam(':_equid', $solicitud['EquId'], PDO::PARAM_INT);
            $stmt->bindParam(':_fecha', $solicitud['Fecha'], PDO::PARAM_STR);
            $stmt->bindParam(':_cliruc', $solicitud['CliRuc'], PDO::PARAM_STR);
            $stmt->bindParam(':_clinombre', $solicitud['CliNombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_clidireccion', $solicitud['CliDireccion'], PDO::PARAM_STR);
            $stmt->bindParam(':_clicontacto', $solicitud['CliContacto'], PDO::PARAM_STR);
            $stmt->bindParam(':_clitelefono', $solicitud['CliTelefono'], PDO::PARAM_STR);
            $stmt->bindParam(':_clicorreo', $solicitud['CliCorreo'], PDO::PARAM_STR);
            $stmt->bindParam(':_supervisor', $solicitud['Supervisor'], PDO::PARAM_STR);
            $stmt->bindParam(':_equcodigo', $solicitud['EquCodigo'], PDO::PARAM_STR);
            $stmt->bindParam(':_equnombre', $solicitud['EquNombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_equmarca', $solicitud['EquMarca'], PDO::PARAM_STR);
            $stmt->bindParam(':_equmodelo', $solicitud['EquModelo'], PDO::PARAM_STR);
            $stmt->bindParam(':_equplaca', $solicitud['EquPlaca'], PDO::PARAM_STR);
            $stmt->bindParam(':_equserie', $solicitud['EquSerie'], PDO::PARAM_STR);
            $stmt->bindParam(':_equmotor', $solicitud['EquMotor'], PDO::PARAM_STR);
            $stmt->bindParam(':_equtransmision', $solicitud['EquTransmision'], PDO::PARAM_STR);
            $stmt->bindParam(':_equdiferencial', $solicitud['EquDiferencial'], PDO::PARAM_STR);
            $stmt->bindParam(':_equkm', $solicitud['EquKm'], PDO::PARAM_INT);
            $stmt->bindParam(':_equhm', $solicitud['EquHm'], PDO::PARAM_INT);
            $stmt->bindParam(':_actividades', $solicitud['Actividades'], PDO::PARAM_STR);
            $stmt->bindParam(':_observaciones', $solicitud['Observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(':_usuario', $solicitud['Usuario'], PDO::PARAM_STR);
            $stmt->execute();

            $stmt = $conmy->query("SELECT @_id as id");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['id'];
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());//sera propagado al catch(Exception $ex) del nivel superior.
        }
    }

    function FnBuscarSolicitud($conmy, $id, $cliid) {
        try {
            $datos=array();

            $stmt = $conmy->prepare("select id, equid, fecha, nombre, cli_ruc, cli_nombre, cli_direccion, cli_contacto, cli_telefono, cli_correo, supervisor, equ_codigo, equ_nombre, 
            equ_marca, equ_modelo, equ_placa, equ_serie, equ_motor, equ_transmision, equ_diferencial, equ_km, equ_hm, actividades, observaciones, estado FROM tblsolicitudes WHERE id=:Id and cliid=:CliId;");
            $stmt->execute(array(':Id'=>$id, ':CliId'=>$cliid));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $datos['id']=$row['id'];
                $datos['equid']=$row['equid'];
                $datos['fecha']=$row['fecha'];
                $datos['nombre']=$row['nombre'];
                $datos['cliruc']=$row['cli_ruc'];
                $datos['clinombre']=$row['cli_nombre'];
                $datos['clidireccion']=$row['cli_direccion'];
                $datos['clicontacto']=$row['cli_contacto'];
                $datos['clitelefono']=$row['cli_telefono'];
                $datos['clicorreo']=$row['cli_correo'];
                $datos['supervisor']=$row['supervisor'];
                $datos['equcodigo']=$row['equ_codigo'];
                $datos['equnombre']=$row['equ_nombre'];
                $datos['equmarca']=$row['equ_marca'];
                $datos['equmodelo']=$row['equ_modelo'];
                $datos['equplaca']=$row['equ_placa'];
                $datos['equserie']=$row['equ_serie'];
                $datos['equmotor']=$row['equ_motor'];
                $datos['equtransmision']=$row['equ_transmision'];
                $datos['equdiferencial']=$row['equ_diferencial'];
                $datos['equkm']=$row['equ_km'];
                $datos['equhm']=$row['equ_hm'];
                $datos['actividades']=$row['actividades'];
                $datos['observaciones']=$row['observaciones'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
      }

    function FnBuscarSolicitudes($conmy, $search) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($search['nombre'])){
                $query = " and nombre like='%".$search['nombre']."%'";
            }else{
                if($search['equid']>0){
                    $query.=" and equid=".$search['equid'];
                }
                $query.=" and fecha between '".$search['fechainicial']."' and '".$search['fechafinal']."'";
            }

            $query.=" limit ".$search['pagina'].", 15";

            $stmt = $conmy->prepare("select id, fecha, nombre, cli_nombre, equ_codigo, actividades, estado from tblsolicitudes where cliid=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$search['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['id'],
                        'fecha'=>$row['fecha'],                        
                        'nombre'=>$row['nombre'],
                        'clinombre'=>$row['cli_nombre'],
                        'equcodigo'=>$row['equ_codigo'],
                        'actividades'=>$row['actividades'],
                        'estado'=>(int)$row['estado']
                    );
                }
                $datos['pag']=$n;
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }

    function FnFinalizarSolicitud($conmy, $solicitud) {
        try {
            $res=false;
            $stmt = $conmy->prepare("update tblsolicitudes set estado=3, actualizacion=:Actualizacion where id=:Id and cliid=:CliId and estado=2;");
            $stmt->execute(array(':Actualizacion'=>$solicitud['Usuario'], ':Id'=>$solicitud['Id'], ':CliId'=>$solicitud['CliId']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res;         
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnModificarSolicitud($conmy, $solicitud) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update tblsolicitudes set actividades=:Actividades, observaciones=:Observaciones, actualizacion=:Actualizacion where id=:Id and cliid=:CliId and estado=2;");
            $stmt->execute(array(':Actividades'=>$solicitud['Actividades'], ':Observaciones'=>$solicitud['Observaciones'], ':Actualizacion'=>$solicitud['Usuario'], ':Id'=>$solicitud['Id'], ':CliId'=>$solicitud['CliId']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarSolicitudEquipo($conmy, $solicitud) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update tblsolicitudes set equ_nombre=:EquNombre, equ_marca=:EquMarca, equ_modelo=:EquModelo, equ_placa=:EquPlaca, equ_serie=:EquSerie, equ_motor=:EquMotor, equ_transmision=:EquTransmision, equ_diferencial=:EquDiferencial, equ_km=:EquKm, equ_hm=:EquHm, actualizacion=:Actualizacion where id=:Id and cliid=:CliId and estado=2;");
            $stmt->execute(array(':EquNombre'=>$solicitud['EquNombre'], ':EquMarca'=>$solicitud['EquMarca'], ':EquModelo'=>$solicitud['EquModelo'], ':EquPlaca'=>$solicitud['EquPlaca'], ':EquSerie'=>$solicitud['EquSerie'], ':EquMotor'=>$solicitud['EquMotor'], ':EquTransmision'=>$solicitud['EquTransmision'], ':EquDiferencial'=>$solicitud['EquDiferencial'], ':EquKm'=>$solicitud['EquKm'], ':EquHm'=>$solicitud['EquHm'], ':Actualizacion'=>$solicitud['Usuario'], ':Id'=>$solicitud['Id'], ':CliId'=>$solicitud['CliId']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarSolicitudCliente($conmy, $solicitud) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update tblsolicitudes set cli_direccion=:CliDireccion, cli_contacto=:CliContacto, cli_telefono=:CliTelefono, cli_correo=:CliCorreo, actualizacion=:Actualizacion where id=:Id and cliid=:CliId and estado=2;");
            $stmt->execute(array(':CliDireccion'=>$solicitud['CliDireccion'], ':CliContacto'=>$solicitud['CliContacto'], ':CliTelefono'=>$solicitud['CliTelefono'], ':CliCorreo'=>$solicitud['CliCorreo'], ':Actualizacion'=>$solicitud['Usuario'], ':Id'=>$solicitud['Id'], ':CliId'=>$solicitud['CliId']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnListarPlantilla($conmy) {
        try {
            $data=array();
            $stmt=$conmy->prepare("select id, nombre from tblchkplantillas;");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[]=array(
                    'id'=>$row['id'],
                    'nombre'=>$row['nombre']
                );
            }
            return $data;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
?>