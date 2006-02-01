<?
require_once('consola/comando.php');
//define('apex_pa_instancia','desarrollo');

class comando_toba extends comando
{
	function get_dir_raiz( $obligatorio = true )
	{
		if( !isset( $_SERVER['toba_dir'] ) ) {
			if( $obligatorio ) {
				throw new excepcion_toba("COMANDO_TOBA: La variable de entorno 'toba_dir' no esta definida");
			} else {
				return null;	
			}
		}
		return $_SERVER['toba_dir'];
	}
	
	function get_entorno_id_instancia( $obligatorio = true )
	{
		if( !isset( $_SERVER['toba_instancia'] ) ) {
			if( $obligatorio ) {
				throw new excepcion_toba("COMANDO_TOBA: La variable de entorno 'toba_instancia' no esta definida");
			} else {
				return null;	
			}
		}
		return $_SERVER['toba_instancia'];
	}

	function get_entorno_id_proyecto( $obligatorio = true )
	{
		if( !isset( $_SERVER['toba_proyecto'] ) ) {
			if( $obligatorio ) {
				throw new excepcion_toba("COMANDO_TOBA: La variable de entorno 'toba_proyecto' no esta definida");
			} else {
				return null;	
			}
		}
		return $_SERVER['toba_proyecto'];
	}
}
?>