<?
require_once("modelo/conversiones/conversion.php");

class conversion_0_9_0 extends conversion
{
	function get_version()
	{
		return "0.9.0";	
	}

	/**
	*	Las claves pasan a encriptarse con md5 (los passwords planos siguen funcionando)
	*/
	function cambio_claves_encriptadas()
	{
		$sql = "UPDATE apex_usuario SET clave=md5(clave), autentificacion='md5' 
				WHERE autentificacion IS NULL OR autentificacion='plano'";
		$this->ejecutar($sql);	
	}
	
	/**
	 * Los items "modernos de toba" (>= 0.8) que utilizan un CI y ocpcionalmente un CN y que
	 * utilizan alguno de los patrones predefinidos para manejarlos se migran a un nuevo
	 * tipo de solicitud (solicitud_web en lugar de la obsoleta solicitud_browser), este cambio
	 * se debe a que el nucelo de toba sufrio una reestructuracion muy grande recayendo
	 * gran parte sobre la solicitud y no se quiere romper la compatilibilidad con los items viejos
	 */
	function cambio_solicitud_web()
	{
		$sql = "UPDATE apex_item
				SET	solicitud_tipo='web'
				WHERE
					proyecto='{$this->proyecto}' AND 
					solicitud_tipo='browser' AND
					actividad_patron IN ('CI', 'CI_POPUP', 'ci', 'ci_cn_popup', 'generico_ci_cn')
		";
		$this->ejecutar($sql);	
	}
}
?>
