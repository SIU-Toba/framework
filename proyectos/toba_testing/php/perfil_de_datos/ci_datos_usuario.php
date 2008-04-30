<?php 
class ci_datos_usuario extends toba_ci
{
	function ini()
	{
		ei_arbol(toba::perfil_de_datos()->get_perfil_datos(),'get_perfil_datos');
		ei_arbol(toba::perfil_de_datos()->perfil_datos_posee_restricciones(),'perfil_datos_posee_restricciones');
		ei_arbol(toba::perfil_de_datos()->get_perfil_datos_restricciones(),'get_perfil_datos_restricciones');
		ei_arbol(toba::perfil_de_datos()->get_perfil_datos_dimensiones_restringidas(),'get_perfil_datos_dimensiones_restringidas');
	}
}

?>