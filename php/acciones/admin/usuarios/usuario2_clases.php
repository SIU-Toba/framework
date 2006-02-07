<?
//################################################################
//################################################################
//#####  Redefino el OBJETO MT utilizado por esta ACTIVIDAD  #####
//################################################################
//################################################################
//El 'detalle_1' (Las propiedades del usuario en el proyecto) 
//tiene una constante en la clave (El proyecto actual)

include_once("nucleo/browser/clases/objeto_mt_s_md.php");
class objeto_mt_mds_usuario extends objeto_mt_mds
{
	function cargar_db($clave)
	{
		//Cargo del MAESTRO
		$status = $this->dependencias['maestro']->cargar_db( $clave );
		if($status){
			//Modifico la clave del DETALLE_1
			$clave_detalle[0] = $this->solicitud->hilo->obtener_proyecto();
			$clave_detalle[1] = $clave[0];			
			if( $this->dependencias['detalle_1']->cargar_db( $clave_detalle )){
				$this->memoria["ut_estado"]['detalle_1'] = "update";
			}else{
				$this->memoria["ut_estado"]['detalle_1'] = "insert";
			}
		}
		$this->memorizar();
		return $status;
	}
	//-------------------------------------------------------------------------------

	function ut_detalle_asignar_clave_maestro($clave)
	{
		$clave_detalle['proyecto'] = $this->solicitud->hilo->obtener_proyecto();
		if(isset($clave['usuario'])){
			$clave_detalle['usuario'] = $clave['usuario'];
		}else{
			$clave_detalle['usuario'] = $clave[0];
		}
		//ei_arbol($clave_detalle);
		$this->dependencias['detalle_1']->cargar_estado_ef( $clave_detalle );
	}
	//-------------------------------------------------------------------------------
}	
//################################################################

?>
