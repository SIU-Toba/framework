<?
//Generacion: 17-06-2005 12:51:56
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto'
{
	function __construct($id, $fuente, $tope_registros=null, $utilizar_transaccion=null, $memoria_autonoma=null)
	{
		$definicion['tabla']='apex_objeto';
		$definicion['clave'][0]='proyecto';
		$definicion['clave'][1]='objeto';
		$definicion['no_nulo'][0]='proyecto';
		$definicion['no_nulo'][1]='clase_proyecto';
		$definicion['no_nulo'][2]='clase';
		$definicion['no_nulo'][3]='nombre';
		$definicion['no_nulo'][4]='fuente_datos_proyecto';
		$definicion['no_nulo'][5]='fuente_datos';
		$definicion['secuencia'][0]['col']='objeto';
		$definicion['secuencia'][0]['seq']='apex_objeto_seq';
		$definicion['columna'][0]='anterior';
		$definicion['columna'][1]='reflexivo';
		$definicion['columna'][2]='clase_proyecto';
		$definicion['columna'][3]='clase';
		$definicion['columna'][4]='subclase';
		$definicion['columna'][5]='subclase_archivo';
		$definicion['columna'][6]='objeto_categoria_proyecto';
		$definicion['columna'][7]='objeto_categoria';
		$definicion['columna'][8]='nombre';
		$definicion['columna'][9]='titulo';
		$definicion['columna'][10]='colapsable';
		$definicion['columna'][11]='descripcion';
		$definicion['columna'][12]='fuente_datos_proyecto';
		$definicion['columna'][13]='fuente_datos';
		$definicion['columna'][14]='solicitud_registrar';
		$definicion['columna'][15]='solicitud_obj_obs_tipo';
		$definicion['columna'][16]='solicitud_obj_observacion';
		$definicion['columna'][17]='parametro_a';
		$definicion['columna'][18]='parametro_b';
		$definicion['columna'][19]='parametro_c';
		$definicion['columna'][20]='parametro_d';
		$definicion['columna'][21]='parametro_e';
		$definicion['columna'][22]='parametro_f';
		$definicion['columna'][23]='usuario';
		$definicion['columna'][24]='creacion';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto = '{$id['objeto']}'";
		$where[] = "proyecto = '{$id['proyecto']}'";
		$this->cargar_datos($where);
	}
}
?>