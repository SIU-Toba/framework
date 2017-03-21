<?php
/**
 * Clase que obtiene y grafica los datos del usuario y aplicaciones a las que tiene acceso
 * 
 * @package SalidaGrafica
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
									'cuentas' => array(),
								);
		
		// datos provenientes de la autenticacion
		$atributos_usuario = toba::manejador_sesiones()->get_autenticacion()->get_atributos_usuario();
		if (isset($atributos_usuario['appLauncherData']) && !empty($atributos_usuario['appLauncherData'])) {			
			$appLauncherData = array_a_latin1(json_decode(current($atributos_usuario['appLauncherData']), true));
			$appLauncherData['usuario_id'] = $this->generar_descripcion_id($appLauncherData, toba::usuario()->get_id());
		} else {
			$appLauncherData = array();
		}
		//recupero las otras cuentas disponibles para el usuario
		$cuentas_disponibles = toba::manejador_sesiones()->get_cuentas_disponibles();
		if (! empty($cuentas_disponibles)) {
			$appLauncherData['cuenta_actual'] = $cuentas_disponibles['usuario_actual'];
			unset($cuentas_disponibles['usuario_actual']);		
			$appLauncherData['cuentas']  = $cuentas_disponibles;
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
		$url_base = toba_recurso::url_toba().'/js/packages/siu-js-app-launcher/';
		
		$html =  '<link rel="stylesheet" href="' . $url_base . 'css/font-awesome-4.4.0/css/font-awesome.min.css" type="text/css" />';
		$html .= '<link rel="stylesheet" href="' . $url_base .'css/app_launcher.css" type="text/css" />';
		$html .= toba_js::incluir($url_base . 'app_launcher.js');
		$html .= '<div id="enc-usuario" class="enc-usuario"></div>';
		$html .= toba_js::ejecutar($this->get_codigo_inicializacion());
		return $html;
	}
	
	/**
	*	Realiza un echo html del app_launcher con informacion del usuario y aplicaciones a las que tiene acceso
	*/
	public function mostrar_html_app_launcher()
	{
		echo $this->get_html_app_launcher();
	}
	
	/**
	 * Devuelve el codigo necesario para inicializar el appLauncher con datos
	 * @return string
	 */
	protected function get_codigo_inicializacion()
	{
		$js = toba_editor::modo_prueba() ? 'window.close()' : 'salir()';		
		return  'appLauncher.init({
				container: "#enc-usuario",
				data: ' . json_encode(array_a_utf8($this->get_app_launcher_data())) . ',
				urlAppUsrChg: '.json_encode(utf8_e_seguro(toba::vinculador()->get_url())).',
				usrChangeParam: '. json_encode(utf8_e_seguro(apex_sesion_qs_cambio_usuario)) .',
				js_salir: function() { '.$js.'},
			});';		
	}
	
	protected function generar_descripcion_id($datos, $id_toba)
	{
		if (isset($datos['usuario_id']) && trim($datos['usuario_id']) != trim($id_toba))  {
			$resultado = $datos['usuario_id'] .  " / Toba: $id_toba";
		} else {
			$resultado = $id_toba;
		}
		return $resultado; 
	}	
}
?>
