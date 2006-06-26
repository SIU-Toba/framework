<?
require_once('nucleo/componentes/interface/objeto_ei.php');
/**
 * Controla un flujo de pantallas
 * @package Objetos
 * @subpackage Ei
 */
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
