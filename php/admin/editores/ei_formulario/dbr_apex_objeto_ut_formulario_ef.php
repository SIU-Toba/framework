<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ut_formulario_ef extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_ut_formulario_ef'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_ut_formulario_ef';
		$def['columna'][0]['nombre']='objeto_ut_formulario_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_ut_formulario';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='identificador';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='columnas';
		$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='clave_primaria';
		$def['columna'][5]['nombre']='obligatorio';
		$def['columna'][6]['nombre']='elemento_formulario';
		$def['columna'][6]['no_nulo']='1';
		$def['columna'][7]['nombre']='inicializacion';
		$def['columna'][8]['nombre']='orden';
		$def['columna'][8]['no_nulo']='1';
		$def['columna'][9]['nombre']='etiqueta';
		$def['columna'][10]['nombre']='descripcion';
		$def['columna'][11]['nombre']='colapsado';
		$def['columna'][12]['nombre']='desactivado';
		$def['columna'][13]['nombre']='no_sql';
		$def['columna'][14]['nombre']='total';
		$def['columna'][15]['nombre']='clave_primaria_padre';
		$def['columna'][16]['nombre']='listar';
		$def['columna'][17]['nombre']='lista_cabecera';
		$def['columna'][18]['nombre']='lista_orden';
		$def['columna'][19]['nombre']='lista_columna_estilo';
		$def['columna'][20]['nombre']='lista_valor_sql';
		$def['columna'][21]['nombre']='lista_valor_sql_formato';
		$def['columna'][22]['nombre']='lista_valor_sql_esp';
		$def['columna'][23]['nombre']='lista_ancho';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_ut_formulario_proyecto = '{$id['objeto_ut_formulario_proyecto']}'";
		$where[] = "objeto_ut_formulario = '{$id['objeto_ut_formulario']}'";
		$where[] = "identificador = '{$id['identificador']}'";
		$this->cargar_datos($where);
	}
}
?>