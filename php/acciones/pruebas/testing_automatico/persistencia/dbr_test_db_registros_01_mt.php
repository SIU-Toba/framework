<?
//Generacion: 23-06-2005 16:53:52
//Fuente de datos: 'comechingones'
require_once('nucleo/persistencia/db_registros_mt.php');

class dbr_test_db_registros_01_mt extends db_registros_mt
//db_registros especifico de la tabla 'test_db_registros_01'
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$definicion['tabla'][0]='test_db_registros_01';
		$definicion['tabla_alias'][0]='t01';
		$definicion['test_db_registros_01']['clave'][0]='id';
		$definicion['test_db_registros_01']['no_nulo'][0]='id';
		$definicion['test_db_registros_01']['no_nulo'][1]='nombre';
		$definicion['test_db_registros_01']['columna'][0]='nombre';
		$definicion['test_db_registros_01']['columna'][1]='descripcion';
		$definicion['tabla'][1]='test_db_registros_02';
		$definicion['tabla_alias'][1]='t02';
		$definicion['test_db_registros_02']['clave'][0]='id';
		$definicion['test_db_registros_02']['no_nulo'][0]='id';
		$definicion['test_db_registros_02']['no_nulo'][1]='extra';
		$definicion['test_db_registros_02']['columna'][0]='extra';
		$definicion['relacion']['test_db_registros_02'][0]['pk'] = 'id';
		$definicion['relacion']['test_db_registros_02'][0]['fk'] = 'id';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "t01.id = '$id'";
		$this->cargar_datos($where);
	}
}
?>