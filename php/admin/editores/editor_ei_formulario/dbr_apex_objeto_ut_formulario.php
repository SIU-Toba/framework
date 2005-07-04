<?
//Generacion: 4-07-2005 00:43:11
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ut_formulario extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_ut_formulario'
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$definicion['tabla']='apex_objeto_ut_formulario';
		$definicion['clave'][0]='objeto_ut_formulario_proyecto';
		$definicion['clave'][1]='objeto_ut_formulario';
		$definicion['no_nulo'][0]='objeto_ut_formulario_proyecto';
		$definicion['no_nulo'][1]='objeto_ut_formulario';
		$definicion['columna'][0]='tabla';
		$definicion['columna'][1]='titulo';
		$definicion['columna'][2]='ev_agregar';
		$definicion['columna'][3]='ev_agregar_etiq';
		$definicion['columna'][4]='ev_mod_modificar';
		$definicion['columna'][5]='ev_mod_modificar_etiq';
		$definicion['columna'][6]='ev_mod_eliminar';
		$definicion['columna'][7]='ev_mod_eliminar_etiq';
		$definicion['columna'][8]='ev_mod_limpiar';
		$definicion['columna'][9]='ev_mod_limpiar_etiq';
		$definicion['columna'][10]='ev_mod_clave';
		$definicion['columna'][11]='clase_proyecto';
		$definicion['columna'][12]='clase';
		$definicion['columna'][13]='auto_reset';
		$definicion['columna'][14]='ancho';
		$definicion['columna'][15]='campo_bl';
		$definicion['columna'][16]='scroll';
		$definicion['columna'][17]='filas';
		$definicion['columna'][18]='filas_agregar';
		$definicion['columna'][19]='filas_undo';
		$definicion['columna'][20]='filas_ordenar';
		$definicion['columna'][21]='alto';
		$definicion['columna'][22]='analisis_cambios';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_ut_formulario = '{$id['objeto']}'";
		$where[] = "objeto_ut_formulario_proyecto = '{$id['proyecto']}'";
		$this->cargar_datos($where);
	}
}
?>