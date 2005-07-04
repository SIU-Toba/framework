<?
//Generacion: 17-06-2005 12:51:56
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_dependencias extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_dependencias'
{
	function __construct($id, $fuente, $tope_registros=null, $utilizar_transaccion=null, $memoria_autonoma=null)
	{
		$definicion['tabla']='apex_objeto_dependencias';
		$definicion['clave'][0]='proyecto';
		$definicion['clave'][1]='objeto_consumidor';
		$definicion['clave'][2]='identificador';
		$definicion['no_nulo'][0]='proyecto';
		$definicion['no_nulo'][1]='objeto_consumidor';
		$definicion['no_nulo'][2]='objeto_proveedor';
		$definicion['no_nulo'][3]='identificador';
		$definicion['columna'][0]='objeto_proveedor';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "objeto_consumidor = '{$id['objeto']}'";
		$where[] = "identificador NOT IN (
							SELECT identificador
							FROM apex_objeto_mt_me_etapa_dep dep
							WHERE 
								dep.objeto_mt_me_proyecto = '{$id['proyecto']}' AND
								dep.objeto_mt_me = '{$id['objeto']}' AND
								dep.identificador = identificador
						)";
		$this->cargar_datos($where);
	}
}
?>