<?
//Generacion: 23-06-2005 16:53:52
//Fuente de datos: 'comechingones'
require_once('nucleo/persistencia/db_registros_s.php');

class test_db_registros_s_seq_dbr extends db_registros_s
//db_registros especifico de la tabla 'test_db_registros_01'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def = array(	
				'tabla' => 'test_maestro',
				'columna' => array( 
						array( 	'nombre'=>'id',
								'secuencia'=>1, 
								'pk'=>1 ),
						array( 	'nombre'=>'nombre',
								'no_nulo'=>1 ),
						array( 	'nombre'=>'descripcion' )
					)
				);
		parent::__construct($def, $fuente, $min_registros, $max_registros );
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "id = '$id'";
		$this->cargar_datos($where);
	}
}
?>