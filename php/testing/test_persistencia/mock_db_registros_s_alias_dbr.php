<?
require_once('nucleo/persistencia/db_registros_s.php');

class mock_db_registros_s_alias_dbr extends db_registros_s
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0)
	{
		$def = 	array (
					'tabla' => 'test',
					'columna' => array( 
						array( 	'nombre'=>'id',
								'pk'=>1 ), 
						array( 	'nombre'=>'nombre',
								'no_nulo'=>1 ),
						array( 'nombre'=>'descripcion' )
					)
				);
		parent::__construct($def, $fuente, $min_registros, $max_registros);
	}	
	
	function get_descripcion()
	{
		return "";
	}	

	function cargar_datos_clave($id)
	{
		$where[] = "test.id = '$id'";
		$this->cargar_datos($where);
	}
	
	function cargar_datos_especificos($id)
	/*
		Para recuperar registros usa la clave de otra tabla
	*/
	{
		$from[] = "test_asoc";
		$where[] = "test_asoc.id = test.id";
		$where[] = "test_asoc.extra = '$id'";
		$this->cargar_datos($where, $from);
	}
}
?>