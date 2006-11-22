<?
php_referencia::instancia()->agregar(__FILE__);
require_once('nucleo/componentes/persistencia/toba_ap_tabla_db.php');

class ap_persona_deportes extends toba_ap_tabla_db
{
	function inicializar()
	{
		/*
			Carga la columna externa 'desc_deporte' base a un SQL
			utilizando como parametro la columna 'deporte'
		*/
		$this->activar_proceso_carga_externa_sql(
							"SELECT nombre FROM ref_deportes WHERE id = '%deporte%';",
							array('deporte'),
							array(array('origen'=>'nombre',
										'destino'=>'desc_deporte')),
							true );
		/*
			Carga la columna externa 'desc_dia_semana' en base a un METODO
			utilizando como parametro la columna 'dia_semana'
		*/
		$this->activar_proceso_carga_externa_dao("get_dia_semana",
							"consultas",
							"operaciones_simples/consultas.php",
							array('dia_semana'),
							array('desc_dia_semana'),
							true );
	}
}
?>