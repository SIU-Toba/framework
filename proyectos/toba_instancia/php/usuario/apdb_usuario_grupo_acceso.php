<?
require_once("nucleo/componentes/persistencia/toba_ap_tabla_db_s.php");

class apdb_usuario_grupo_acceso extends toba_ap_tabla_db_s
{
	function inicializar()
	{
		$this->activar_proceso_carga_externa_dao(	'get_descripcion_grupo_acceso',
													'consultas_instancia',
													'lib/consultas_instancia.php',
													array('proyecto','usuario_grupo_acc'), 
													array('grupo_acceso','grupo_acceso_desc') );
	}
}	
?>