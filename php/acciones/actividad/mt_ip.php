<?
require_once("nucleo/browser/clases/objeto_mt_s.php");
require_once("nucleo/browser/clases/objeto_ut_multicheq.php");


##############################################################################

class objeto_mt_s_ip extends objeto_mt_s
{
	function objeto_mt_s_ip($id,&$solicitud)
	{
		parent::objeto_mt_s($id, $solicitud);
	}
	//-------------------------------------------------------------------------------

	function procesar()
	{
		$this->dependencias['ip']->cargar_datos();
		if($this->controlar_activacion()){
        	$this->dependencias['ip']->info_estado();
        	$this->cargar_post();
        	ei_arbol($this->dependencias['ip']->obtener_datos(),"datos");
        	ei_arbol($this->dependencias['ip']->obtener_seleccionados(),"seleccionados");
			//ei_arbol($_POST,"post");
 		}
 	}
}

##############################################################################

class objeto_ut_multicheq_ip extends objeto_ut_multicheq
{
	function objeto_ut_multicheq_ip($id,&$solicitud)
	{	
		parent::objeto_ut_multicheq($id, $solicitud);	
	}
	//-------------------------------------------------------------------------------

	function evaluar_chequeo_inicial($control)
/*
 	@@acceso: objeto
	@@desc: Evalua si el estado inicial de la linea es ACTIVADO o DESACTIVADO
        @@param: mixed | valor a evaluar
	@@retorno: boolean | true si se debe aparecer, false el en caso contrario
*/
	{
		if($control == 1){
			return true;
		}else{
			return false;
		}
	}
}

##############################################################################
?>