<?
//Generacion: 23-07-2005 05:03:41
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ei_formulario_ef extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_ei_formulario_ef'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_ei_formulario_ef';
		$def['columna'][0]['nombre']='objeto_ei_formulario_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_ei_formulario';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='objeto_ei_formulario_fila';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['secuencia']='apex_obj_ei_form_fila_seq';
		$def['columna'][3]['nombre']='identificador';
		$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='elemento_formulario';
		$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='columnas';
		$def['columna'][5]['no_nulo']='1';
		$def['columna'][6]['nombre']='obligatorio';
		$def['columna'][7]['nombre']='inicializacion';
		$def['columna'][8]['nombre']='orden';
		$def['columna'][8]['no_nulo']='1';
		$def['columna'][9]['nombre']='etiqueta';
		$def['columna'][10]['nombre']='descripcion';
		$def['columna'][11]['nombre']='colapsado';
		$def['columna'][12]['nombre']='desactivado';
		$def['columna'][13]['nombre']='estilo';
		$def['columna'][14]['nombre']='total';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_ei_formulario_proyecto = '{$id['objeto_ei_formulario_proyecto']}'";
		$where[] = "objeto_ei_formulario = '{$id['objeto_ei_formulario']}'";
		$where[] = "objeto_ei_formulario_fila = '{$id['objeto_ei_formulario_fila']}'";
		$this->cargar_datos($where);
	}
}
?>