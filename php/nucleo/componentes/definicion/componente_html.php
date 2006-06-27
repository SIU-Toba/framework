<?
require_once("componente.php");

class componente_html extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_html';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//---- Plan -----------------------
		$sql["info_html"]["sql"] = "SELECT	html      
									FROM	apex_objeto_html
									WHERE	objeto_html_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_html"]["sql"] .= " AND     objeto_html='$componente' ";
		}
		$sql["info_html"]["sql"] .= " ; ";
		$sql["info_html"]["registros"]='1';
		$sql["info_html"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/transversales';
	}
}
?>