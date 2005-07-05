<?
//Generacion: 23-06-2005 16:53:52
//Fuente de datos: 'comechingones'
require_once('nucleo/persistencia/db_registros_s.php');

class test_db_registros_std_s_1_dbr extends db_registros_s
//db_registros especifico de la tabla 'test_db_registros_01'
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$def = array(	
				'tabla' => 'test_maestro',
				'columna' => array( 
						array( 	'nombre'=>'id',
								'pk'=>1, 
								'no_nulo'=>1 ),
						array( 	'nombre'=>'nombre',
								'no_nulo'=>1 ),
						array( 	'nombre'=>'descripcion' )
					)
				);
		parent::__construct($id, $def, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "id = '$id'";
		$this->cargar_datos($where);
	}
}
?>