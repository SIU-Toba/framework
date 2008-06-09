<?php 
class ci_errores_db extends toba_ci
{
	protected $sentencias =  array(
		'clave_duplicada' => "INSERT INTO apex_elemento_formulario (elemento_formulario, descripcion, proyecto) VALUES ('ef', 'ef base', 'toba')",
		'foreign_key' => "INSERT INTO apex_elemento_formulario (elemento_formulario, descripcion, proyecto) VALUES ('ef_algo', 'ef base', 'toba_blabla')",
	
	
	);
	
	
	
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ejecutar_sentencias()
	{
		$mensajes = '<ul>';
		foreach ($this->sentencias as $id => $sentencia) {
			try {
				toba::db()->ejecutar($sentencia);
			} catch (toba_error_db $e) {
				$mensajes .= '<li>'.$e->getMessage().'</li>';
				//$mensajes .= '<li>'.$e->get_mensaje().'</li>';
			}
		}		
		$mensajes .= '</ul>';
		$this->pantalla()->set_descripcion($mensajes);
	}
	
	function evt__con_parseo()
	{
		toba::db()->set_parser_errores(new toba_parser_error_db_postgres7(null));
		$this->ejecutar_sentencias();
	}

	function evt__sin_parseo()
	{
		$this->ejecutar_sentencias();
	}
}

?>