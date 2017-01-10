<?php
/**
 * Tipo de p�gina pensado para pantallas de login, presenta un logo y un pie de p�gina b�sico
 * 
 * @package SalidaGrafica
 */
class toba_tp_logon extends toba_tp_basico
{
	
	function inicio_barra_superior()
	{
		echo toba::output()->get('PaginaLogon')->getInicioBarraSuperior();
	}
	
	function fin_barra_superior()
	{
		echo toba::output()->get('PaginaLogon')->getFinBarraSuperior();
	}

	function pre_contenido()
	{
		echo toba::output()->get('PaginaLogon')->getPreContenido();		
	}

	function post_contenido()
	{
		echo toba::output()->get('PaginaLogon')->getPostContenido();
	}
	
	function footer()
	{
		echo toba::output()->get('PaginaLogon')->getFooterHtml();
	}
}
?>