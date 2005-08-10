<?
require_once("nucleo/browser/clases/objeto_ei.php");						//Ancestro de todos los OE

class objeto_html extends objeto_ei
/*
	@@acceso: publico
	@@desc: Permite representar planeamientos en el eje del tiempo
*/
{
	
//################################################################################
//###########################                         ############################
//###########################      INICIALIZACION     ############################
//###########################                         ############################
//################################################################################
    	
	function objeto_html($id)
/*
	@@acceso: publico
	@@desc: Constructor de la clase
*/
	{
		parent::objeto($id);
		$this->objeto_js = "objeto_cuadro_{$id[1]}";
	}

	/* TODO ESTO TIENE QUE IR A EI!!!!!!!!!! */

	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];		
	}
	function cargar_datos(){}
	function get_lista_eventos(){
		 return array();
	}
	function disparar_eventos(){}
	/*----------------------------------------*/
	
	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//---- Plan -----------------------
		$sql["info_html"]["sql"] = "SELECT	html      
									FROM	apex_objeto_html
									WHERE	objeto_html_proyecto='".$this->id[0]."'
               				AND     objeto_html='".$this->id[1]."';";
		$sql["info_html"]["tipo"]="1";
		$sql["info_html"]["estricto"]="1";
		return $sql;
	}

//################################################################################
//###########################                         ############################
//###########################         INTERFACE       ############################
//###########################                         ############################
//################################################################################


	function obtener_html()
/*
	@@acceso: publico
	@@desc: Genera la interface de este elemento
*/
	{
		ei_nota($this->info_html['html'],"col-tex-p1");
	}
}
//################################################################################
?>
