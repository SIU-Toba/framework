<?php
/**
 * Clase que obtiene y grafica los datos del usuario y aplicaciones a las que tiene acceso
 * 
 * @package Centrales
 */
class toba_app_launcher
{
	static private $instancia;
	protected $appLauncherData;
		
	/**
	 * @return toba_app_launcher
	 */
	static function instancia($recargar=false)
	{
		if (!isset(self::$instancia) || $recargar) {
			self::$instancia = new toba_app_launcher($recargar);	
		}
		return self::$instancia;	
	}

	/**
	 * Elimina la instancia del app_launcher
	 */
	static function eliminar_instancia()
	{
		self::$instancia = null;
	}

	/**
	 * Constructor del app_launcher
	 */
	private function __construct($recargar)
	{
		if(!$this->appLauncherData || $recargar) {
			$this->appLauncherData = $this->cargar_app_launcher_data();
		}
	}
	
	/**
	 * Metodo que retorna los datos para cargar el app_launcher, se obtienen del usuario y de la clase de autenticacion
	 */
	private function cargar_app_launcher_data()
	{
		// datos por defecto
		$appLauncherDataDefault = array(
									'usuario_id' => toba::usuario()->get_id(),
									'usuario_nombre' => toba::usuario()->get_nombre(),
									'usuario_foto' => toba_recurso::imagen_toba('usuarios/foto_perfil_defecto.png'),
									'aplicaciones' => array(),
								);
		
		// datos provenientes de la autenticacion
		$atributos_usuario = toba::manejador_sesiones()->get_autenticacion()->get_atributos_usuario();
		if (isset($atributos_usuario['appLauncherData']) && !empty($atributos_usuario['appLauncherData'])) {
			$appLauncherData = json_decode(current($atributos_usuario['appLauncherData']), true);
		} else {
			$appLauncherData = array();
		}
		
		// mergeo entre los datos por defecto y los datos de la autenticacion
		return array_merge($appLauncherDataDefault, $appLauncherData);
	}
	
	/**
	*	Retorna un array con informacion del usuario y aplicaciones a las que tiene acceso
	*	@return Array de datos del usuario y aplicaciones
	*/
	public function get_app_launcher_data()
	{
		return $this->appLauncherData;
	}
	
	/**
	*	Setea el appLauncherData con informacion del usuario y aplicaciones a las que tiene acceso
	*/
	public function set_app_launcher_data($appLauncherData)
	{
		$this->appLauncherData = $appLauncherData;
	}
	
	/**
	*	Retorna el html que grafica el app_launcher con informacion del usuario y aplicaciones a las que tiene acceso
	*	@return string	html del app_launcher
	*/
	public function get_html_app_launcher()
	{
		$js = toba_editor::modo_prueba() ? 'window.close()' : 'salir()';
		
		$url_js_app_launcher = toba::instalacion()->get_url().'/js/js-app-launcher/';
		
		$html =  '	<link rel="stylesheet" href="' . $url_js_app_launcher . 'css/font-awesome-4.4.0/css/font-awesome.min.css" type="text/css" />';
		$html .= '	<link rel="stylesheet" href="' . $url_js_app_launcher .'css/app_launcher.css" type="text/css" />';
		$html .= toba_js::incluir($url_js_app_launcher . 'app_launcher.js');
		$html .= '	<div id="enc-usuario" class="enc-usuario">';
		$html .= '	</div>';
        $html .= '	<script>
					appLauncher.init({
						container: "#enc-usuario",
						data: ' . json_encode($this->get_app_launcher_data()) . ',
						js_salir: function() { javascript:'.$js.'},
					});
				</script>';
		return $html;
	}
	
	/**
	*	Realiza un echo html del app_launcher con informacion del usuario y aplicaciones a las que tiene acceso
	*/
	public function mostrar_html_app_launcher()
	{
		echo $this->get_html_app_launcher();
	}
	
}

?>