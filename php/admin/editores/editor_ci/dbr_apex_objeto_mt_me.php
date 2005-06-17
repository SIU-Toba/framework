<?
//Generacion: 17-06-2005 12:51:57
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_mt_me extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_mt_me'
{
	function __construct($id, $fuente, $tope_registros=null, $utilizar_transaccion=null, $memoria_autonoma=null)
	{
		$definicion['tabla']='apex_objeto_mt_me';
		$definicion['clave'][0]='objeto_mt_me_proyecto';
		$definicion['clave'][1]='objeto_mt_me';
		$definicion['no_nulo'][0]='objeto_mt_me_proyecto';
		$definicion['no_nulo'][1]='objeto_mt_me';
		$definicion['columna'][0]='ev_procesar_etiq';
		$definicion['columna'][1]='ev_cancelar_etiq';
		$definicion['columna'][2]='ancho';
		$definicion['columna'][3]='alto';
		$definicion['columna'][4]='tipo_navegacion';
		$definicion['columna'][5]='incremental';
		$definicion['columna'][6]='debug_eventos';
		$definicion['columna'][7]='activacion_procesar';
		$definicion['columna'][8]='activacion_cancelar';
		$definicion['columna'][9]='ev_procesar';
		$definicion['columna'][10]='ev_cancelar';
		$definicion['columna'][11]='objetos';
		$definicion['columna'][12]='post_procesar';
		$definicion['columna'][13]='metodo_despachador';
		$definicion['columna'][14]='metodo_opciones';
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