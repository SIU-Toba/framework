<?
require_once("nucleo/browser/clases/objeto_cuadro.php");
class objeto_cuadro_solicitudes extends objeto_cuadro
/**
 * @acceso: nucleo
 * @desc: Extension de la clase ABMS para realizar validaciones especificas
 */
{
    
     function objeto_cuadro_solicitudes($id, & $solicitud)
    /**
     * @acceso: nucleo
     * @desc: Muestra la definicion del OBJETO
     */
    {
         parent :: objeto_cuadro($id, $solicitud);
    }
    
    
     function procesar_celda_cronometro($fila, $valor)
    /**
     * @acceso: nucleo
     * @desc: Regenera el valor del dato a mostrar en una celda
     */
    {
		
		if ($this->datos[$fila]['cronometro'] > 1) {
			$clave = $this->datos[$fila]['solicitud'];    
			$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto( $this->id,
																			"cron",
																			$clave, 
																			true);
			return $vinculo;																
		}else{
			return null;
		}
    }
	
	function procesar_celda_observacion($fila, $valor)
    /**
     * @acceso: nucleo
     * @desc: Regenera el valor del dato a mostrar en una celda
     */
    {
		
		if ($this->datos[$fila]['observacion'] > 0) {
			$clave = $this->datos[$fila]['solicitud'];    
			$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto($this->id,
																			"observaciones",
																			$clave,
																			true);
			return $vinculo;							
		}else{
			return null;
		}
    }
    
	function procesar_celda_observacion_obj($fila, $valor)
    /**
     * @acceso: nucleo
     * @desc: Regenera el valor del dato a mostrar en una celda
     */
    {
		if ($this->datos[$fila]['observacion_obj'] > 0) {
			$clave = $this->datos[$fila]['solicitud'];    
			$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto($this->id,
																			"observaciones_obj",
																			$clave,
																			true);
			return $vinculo;
		}else{
			return null;
		}
    }

	function procesar_celda_wddx($fila, $valor)
    /**
     * @acceso: nucleo
     * @desc: Regenera el valor del dato a mostrar en una celda
     */
    {
		if ($this->datos[$fila]['wddx'] > 0) {
			$clave = $this->datos[$fila]['solicitud'];    
			$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto($this->id,
																			"wddx",
																			$clave,
																			true);
			return $vinculo;
		}else{
			return null;
		}
    }
}

# ########################################################################### 
// Adjunto al where el codigo de sesion browser
$where = null;

 if($sesion = $this->hilo->obtener_parametro("sesion")){
     $where[] = "se.sesion_browser = '$sesion'";
     }

 $cuadro = $this -> cargar_objeto("objeto_cuadro", 0);
 if($cuadro > -1)
{
     $this->objetos[$cuadro]->cargar_datos($where);
     enter();
     $this->objetos[$cuadro]->obtener_html();
     enter();
     }else{
     echo ei_mensaje("No se pudo cargar el cuadro asociado");
     }
?> 		
