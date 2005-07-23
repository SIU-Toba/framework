<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ei_cuadro_columna extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_cuadro_columna'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_ei_cuadro_columna';
		$def['columna'][0]['nombre']='objeto_cuadro_proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_cuadro';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='orden';
		//$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='titulo';
		//$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='estilo';
		//$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='ancho';
		$def['columna'][6]['nombre']='total';
		$def['columna'][7]['nombre']='formateo';
		$def['columna'][8]['nombre']='clave';
		//$def['columna'][8]['no_nulo']='1';
		$def['columna'][9]['nombre']='vinculo_indice';
		$def['columna'][10]['nombre']='no_ordenar';
		$def['columna'][11]['nombre']='mostrar_xls';
		$def['columna'][12]['nombre']='mostrar_pdf';
		$def['columna'][13]['nombre']='pdf_propiedades';
		$def['columna'][14]['nombre']='desabilitado';
		$def['columna'][15]['nombre']='objeto_cuadro_col';
		$def['columna'][15]['pk']='1';
		$def['columna'][15]['secuencia']='apex_obj_ei_cuadro_col_seq';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_cuadro_proyecto = '{$id['proyecto']}'";
		$where[] = "objeto_cuadro = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>