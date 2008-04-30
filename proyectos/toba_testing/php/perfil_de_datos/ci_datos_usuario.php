<?php 
class ci_datos_usuario extends toba_ci
{
	function ini()
	{
		ei_arbol(toba::perfil_de_datos()->get_id(),'get_id');
		ei_arbol(toba::perfil_de_datos()->posee_restricciones(),'posee_restricciones');
		ei_arbol(toba::perfil_de_datos()->get_restricciones(),'get_restricciones');
		ei_arbol(toba::perfil_de_datos()->get_lista_dimensiones_restringidas(),'get_lista_dimensiones_restringidas');
		ei_arbol(toba::perfil_de_datos()->get_gatillos_activos(),'get_gatillos_activos');
		toba::perfil_de_datos()->dump();
	}
}

?>