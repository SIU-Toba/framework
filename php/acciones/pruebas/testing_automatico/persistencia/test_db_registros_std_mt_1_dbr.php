<?
require_once('nucleo/persistencia/db_registros_mt.php');

class test_db_registros_std_mt_1_dbr extends db_registros_mt
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$def = 	array(	
					array (
						'nombre' => 'test_maestro',
						'alias' => 'maestro',
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
						'nombre' => 'test_detalle',
						'alias' => 'detalle',
						'columna' => array( 
							array( 	'nombre'=>'id',
									'pk'=>1, 
									'no_nulo'=>1,
									'join'=>'id' ),
							array( 'nombre'=>'extra',
									'no_nulo'=>1 )
						)
					)
				);
		parent::__construct($id, $def, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "maestro.id = '$id'";
		$this->cargar_datos($where);
	}
}
?>