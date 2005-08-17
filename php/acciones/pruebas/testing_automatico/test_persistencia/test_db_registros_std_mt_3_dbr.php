<?
require_once('nucleo/persistencia/db_registros_mt.php');

class test_db_registros_std_mt_3_dbr extends db_registros_mt
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0)
	{
		$def = 	array(	
					array (
						'tabla' => 'test_maestro',
						'columna' => array( 
							array( 	'nombre'=>'id',
									'pk'=>1, 
									'no_nulo'=>1 ),
							array( 	'nombre'=>'nombre',
									'no_nulo'=>1 ),
							array( 'nombre'=>'descripcion' )
						)
					),
					array (
						'tabla' => 'test_detalle',
						'columna' => array( 
							array( 	'nombre'=>'id',
									'pk'=>1, 
									'no_nulo'=>1,
									'join'=>'id' ),
							array( 'nombre'=>'extra')
						)
					),
					array (
						'tabla' => 'test_detalle_2',
						'columna' => array( 
							array( 	'nombre'=>'id',
									'pk'=>1, 
									'no_nulo'=>1,
									'join'=>'id' ),
							array( 'nombre'=>'variacion')
						)
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
		$where[] = "test_maestro.id = '$id'";
		$this->cargar_datos($where);
	}
}
?>