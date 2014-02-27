<?php

class toba_servicio_web_cliente
{
	protected $wsf;
	protected $opciones;
	protected $id_servicio;
	protected $proyecto;
	
	protected static $modelo_proyecto;
	protected static function get_modelo_proyecto($proyecto_id)
	{
		if (! isset(self::$modelo_proyecto)) {
			$modelo = toba_modelo_catalogo::instanciacion();	
			$modelo->set_db(toba::db());	
			self::$modelo_proyecto = $modelo->get_proyecto(toba::instancia()->get_id(), $proyecto_id);
		}		
	}
	
	/**
	 * @return toba_servicio_web_cliente
	 */
	static function conectar($id_servicio, $opciones=array(), $proyecto = null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_editor::activado() ? toba_editor::get_proyecto_cargado() : toba::proyecto()->get_id();
		}
		$info = toba::proyecto()->get_info_servicios_web_acc($id_servicio, $proyecto);
		$opciones_ini = $info['parametros'];
		
		self::get_modelo_proyecto($proyecto);
		$ini = toba_modelo_servicio_web::get_ini_cliente(self::$modelo_proyecto, $id_servicio);
		if ($ini->existe_entrada('conexion')) {
			$opciones_ini = array_merge($opciones_ini, $ini->get_datos_entrada('conexion'));
		}
		
		if ($info['tipo'] == 'soap') {
			$security_token = self::get_ws_token($proyecto, $id_servicio);
			if (isset($security_token)) {
				$seguridad = array(
						"sign" => true,
						"encrypt" => true,
						"algorithmSuite" => "Basic256Rsa15",
						"securityTokenReference" => "IssuerSerial");

				$policy = new WSPolicy(array("security" => $seguridad));
				$opciones_ini['policy'] = $policy;
				$opciones_ini['securityToken'] = $security_token;
			}
		}
		
		//Convierte todos los '1' de texto en true
		foreach (array_keys($opciones_ini) as $id_opcion) {
			if ($opciones_ini[$id_opcion] === '1' || $opciones_ini[$id_opcion] === 1) {
				$opciones_ini[$id_opcion] = true;
			}
		}		

		//-- Mezcla con las opciones recibidas y crea el objeto
		$opciones = array_merge($opciones_ini, $opciones);
		if (! isset($opciones['to'])) {
			throw new toba_error_def("Debe indicar la URL destino en el campo 'to'");			
		}
		if (isset($opciones['seguro']) && $opciones['seguro'] && ! isset($seguridad)) {
			throw new toba_error("El servicio web esta configurado para requerir firma, sin embargo no se <a target='_blank' href='http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb/Seguridad#configuracion'>configuro correctamente</a> el servicio importando el certificado del servidor.");			
		}
		toba::logger()->debug("Invocando servicio $id_servicio. Opciones:<br>". var_export($opciones, true));
		
		switch ($info['tipo']) {
			case 'soap':
				$servicio = new toba_servicio_web_cliente_soap($opciones, $id_servicio);
				break;
			case 'rest':
				$servicio = new toba_servicio_web_cliente_rest($opciones, $id_servicio);
				break;
			default:
				throw new toba_error_def("No existe el cliente de servicio_web de tipo ".$info['tipo']);
		}
		
		return $servicio;
	}
	
	function __construct($opciones, $id_servicio, $proyecto = null) 
	{
		if (! isset($proyecto)) {
			$proyecto = toba_editor::activado() ? toba_editor::get_proyecto_cargado() : toba::proyecto()->get_id();
		}
		$this->proyecto = $proyecto;
		$this->opciones = $opciones;
		$this->id_servicio = $id_servicio;
	}
	
}
?>