<?
require_once("nucleo/browser/clases/objeto_cuadro.php");
class objeto_cuadro_solicitud_sesion extends objeto_cuadro
/**
 * @acceso: nucleo
 * @desc: Extension de la clase ABMS para realizar validaciones especificas
 */
{
    
     function objeto_cuadro_solicitud_sesion($id, & $solicitud)
    /**
     * @acceso: nucleo
     * @desc: Muestra la definicion del OBJETO
     */
    {
         parent :: objeto_cuadro($id, $solicitud);
    }
    
    
     function procesar_celda_solicitud($fila, $valor)
    /**
     * @acceso: nucleo
     * @desc: Regenera el valor del dato a mostrar en una celda
     */
    {
		if ($this->datos[$fila]['solicitudes'] > 0) {
			$clave = $this->datos[$fila]['sesion'];    
			$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto( $this->id,
																			"solicitud",
																			$clave, 
																			true);
			return $vinculo;																
		}else{
			return null;
		}
    }
}

class objeto_cuadro_solicitud_consola extends objeto_cuadro
/**
 * @acceso: nucleo
 * @desc: Extension de la clase ABMS para realizar validaciones especificas
 */
{
	function procesar_celda_solicitud($fila, $valor)
    /**
     * @acceso: nucleo
     * @desc: Regenera el valor del dato a mostrar en una celda
     */
    {
		$clave   = $this->datos[$fila]['solicitud'];    
		$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto( $this->id,
																			"consola",
																			$clave, 
																			true);
		return $vinculo;
    }
}
	
# ########################################################################### 

?>