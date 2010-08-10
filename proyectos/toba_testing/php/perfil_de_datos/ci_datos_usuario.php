<?php 
class ci_datos_usuario extends toba_testing_pers_ci
{
	function ini()
	{
		$fuente = toba::proyecto()->get_parametro('fuente_datos');
		ei_arbol(toba::perfil_de_datos()->get_id(),'get_id');
		ei_arbol(toba::perfil_de_datos()->posee_restricciones($fuente),'posee_restricciones');
		ei_arbol(toba::perfil_de_datos()->get_restricciones($fuente),'get_restricciones');
		ei_arbol(toba::perfil_de_datos()->get_lista_dimensiones_restringidas($fuente),'get_lista_dimensiones_restringidas');
		ei_arbol(toba::perfil_de_datos()->get_gatillos_activos($fuente),'get_gatillos_activos');
	}
}

?>