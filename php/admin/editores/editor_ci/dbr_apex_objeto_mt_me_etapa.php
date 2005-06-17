<?
//Generacion: 17-06-2005 12:51:57
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_mt_me_etapa extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_mt_me_etapa'
{
	function __construct($id, $fuente, $tope_registros=null, $utilizar_transaccion=null, $memoria_autonoma=null)
	{
		$definicion['tabla']='apex_objeto_mt_me_etapa';
		$definicion['clave'][0]='objeto_mt_me_proyecto';
		$definicion['clave'][1]='objeto_mt_me';
		$definicion['clave'][2]='posicion';
		$definicion['no_nulo'][0]='objeto_mt_me_proyecto';
		$definicion['no_nulo'][1]='objeto_mt_me';
		$definicion['no_nulo'][2]='posicion';
		$definicion['columna'][0]='etiqueta';
		$definicion['columna'][1]='descripcion';
		$definicion['columna'][2]='tip';
		$definicion['columna'][3]='imagen_recurso_origen';
		$definicion['columna'][4]='imagen';
		$definicion['columna'][5]='objetos';
		$definicion['columna'][6]='objetos_adhoc';
		$definicion['columna'][7]='pre_condicion';
		$definicion['columna'][8]='post_condicion';
		$definicion['columna'][9]='gen_interface_pre';
		$definicion['columna'][10]='gen_interface_post';
		$definicion['columna'][11]='ev_procesar';
		$definicion['columna'][12]='ev_cancelar';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_mt_me = '{$id['objeto']}'";
		$where[] = "objeto_mt_me_proyecto = '{$id['proyecto']}'";
		$this->cargar_datos($where);
	}
}
?>