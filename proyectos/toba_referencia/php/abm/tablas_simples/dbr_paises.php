<?
//Generacion: 19-07-2005 11:26:29
//Fuente de datos: 'referencia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_paises extends db_registros_s
//db_registros especifico de la tabla 'paises'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='paises';
		$def['columna'][0]['nombre']='pais';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='nombre';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='codigoiso';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "pais = '$id'";
		$this->cargar_datos($where);
	}
}
?>