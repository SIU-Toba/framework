<?
//Generacion: 5-08-2005 17:08:50
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_item extends db_registros_s
//db_registros especifico de la tabla 'apex_item'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_item';
		$def['columna'][0]['nombre']='item_id';
		$def['columna'][0]['secuencia']='apex_item_seq';
		$def['columna'][1]['nombre']='proyecto';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='item';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='padre_id';
		$def['columna'][4]['nombre']='padre_proyecto';
		$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='padre';
		$def['columna'][5]['no_nulo']='1';
		$def['columna'][6]['nombre']='carpeta';
		$def['columna'][7]['nombre']='nivel_acceso';
		$def['columna'][7]['no_nulo']='1';
		$def['columna'][8]['nombre']='solicitud_tipo';
		$def['columna'][8]['no_nulo']='1';
		$def['columna'][9]['nombre']='pagina_tipo_proyecto';
		$def['columna'][9]['no_nulo']='1';
		$def['columna'][10]['nombre']='pagina_tipo';
		$def['columna'][10]['no_nulo']='1';
		$def['columna'][11]['nombre']='nombre';
		$def['columna'][11]['no_nulo']='1';
		$def['columna'][12]['nombre']='descripcion';
		$def['columna'][13]['nombre']='actividad_buffer_proyecto';
		$def['columna'][13]['no_nulo']='1';
		$def['columna'][14]['nombre']='actividad_buffer';
		$def['columna'][14]['no_nulo']='1';
		$def['columna'][15]['nombre']='actividad_patron_proyecto';
		$def['columna'][15]['no_nulo']='1';
		$def['columna'][16]['nombre']='actividad_patron';
		$def['columna'][16]['no_nulo']='1';
		$def['columna'][17]['nombre']='actividad_accion';
		$def['columna'][18]['nombre']='menu';
		$def['columna'][19]['nombre']='orden';
		$def['columna'][20]['nombre']='solicitud_registrar';
		$def['columna'][21]['nombre']='solicitud_obs_tipo_proyecto';
		$def['columna'][22]['nombre']='solicitud_obs_tipo';
		$def['columna'][23]['nombre']='solicitud_observacion';
		$def['columna'][24]['nombre']='solicitud_registrar_cron';
		$def['columna'][25]['nombre']='prueba_directorios';
		$def['columna'][26]['nombre']='zona_proyecto';
		$def['columna'][27]['nombre']='zona';
		$def['columna'][28]['nombre']='zona_orden';
		$def['columna'][29]['nombre']='zona_listar';
		$def['columna'][30]['nombre']='imagen_recurso_origen';
		$def['columna'][31]['nombre']='imagen';
		$def['columna'][32]['nombre']='parametro_a';
		$def['columna'][33]['nombre']='parametro_b';
		$def['columna'][34]['nombre']='parametro_c';
		$def['columna'][35]['nombre']='publico';
		$def['columna'][36]['nombre']='usuario';
		$def['columna'][37]['nombre']='creacion';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "item = '{$id['item']}'";
		$this->cargar_datos($where);
	}
}
?>