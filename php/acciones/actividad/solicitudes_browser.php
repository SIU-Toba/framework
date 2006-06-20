<?
require_once("nucleo/browser/clases/objeto_cuadro_reg.php");

# ########################################################################### 
// Adjunto al where el codigo de sesion browser
$where = null;

 if($sesion = $this->hilo->obtener_parametro("solicitud")){
     $where[] = "se.sesion_browser = '$sesion'";
     }

 $cuadro = $this -> cargar_objeto("objeto_cuadro", 0);
 if($cuadro > -1)
{
     $this->objetos[$cuadro]->cargar_datos($where);
     enter();
	 $this->objetos[$cuadro]->obtener_html();
     }else{
     echo ei_mensaje("No se pudo cargar el cuadro asociado");
     }
?> 		
