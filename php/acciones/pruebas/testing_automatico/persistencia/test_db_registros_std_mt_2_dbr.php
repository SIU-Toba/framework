<?
require_once('nucleo/persistencia/db_registros_mt.php');

class test_db_registros_std_mt_2_dbr extends db_registros_mt
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$definicion['tabla'][0]='test_maestro';
		$definicion['tabla_alias'][0]='maestro';
		$definicion['test_maestro']['clave'][0]='id1';
		$definicion['test_maestro']['clave'][1]='id2';
		$definicion['test_maestro']['no_nulo'][0]='id1';
		$definicion['test_maestro']['no_nulo'][1]='id2';
		$definicion['test_maestro']['no_nulo'][2]='nombre';
		$definicion['test_maestro']['columna'][0]='nombre';
		$definicion['test_maestro']['columna'][1]='descripcion';
		$definicion['tabla'][1]='test_detalle';
		$definicion['tabla_alias'][1]='detalle';
		$definicion['test_detalle']['clave'][0]='id1';
		$definicion['test_detalle']['clave'][1]='id2';
		$definicion['test_detalle']['no_nulo'][0]='id1';
		$definicion['test_detalle']['no_nulo'][1]='id2';
		$definicion['test_detalle']['no_nulo'][2]='extra';
		$definicion['test_detalle']['columna'][0]='extra';
		$definicion['relacion']['test_detalle'][0]['pk'] = 'id1';
		$definicion['relacion']['test_detalle'][0]['fk'] = 'id1';
		$definicion['relacion']['test_detalle'][1]['pk'] = 'id2';
		$definicion['relacion']['test_detalle'][1]['fk'] = 'id2';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "maestro.id1 = '{$id['id1']}'";
		$where[] = "maestro.id2 = '{$id['id2']}'";
		$this->cargar_datos($where);
	}
}
?>