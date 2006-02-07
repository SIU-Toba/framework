<?
require_once("componente.php");

class componente_lista extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[1]['tabla'] = 'apex_objeto_lista';
		$estructura[1]['registros'] = '1';
		$estructura[1]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		$sql["info_lista"]["sql"] = "SELECT titulo as titulo,
						subtitulo as				subtitulo,
						sql  as						sql,
						col_ver as					col_ver,
						col_formato as				col_formato,
						col_titulos as				col_titulos,
						ancho as					ancho,
						ordenar as					ordenar,
						exportar as					exportar,
						vinculo_clave as			vinculo_clave,
						vinculo_indice as			vinculo_indice
				FROM	apex_objeto_lista
				WHERE	objeto_lista_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_lista"]["sql"] .= " AND 	objeto_lista= '$componente' ";
		}
		$sql["info_lista"]["sql"] .= " ; ";
		$sql["info_lista"]["registros"]='1';
		$sql["info_lista"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/runtime/transversales';
	}
}
?>