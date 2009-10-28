<?php
/**
 * Interface que debe implementar una extensión o redefinición de toba::usuario()
 * @package Centrales
 */
interface toba_interface_usuario
{
	/**
	 * Determina si la clave de un usuario es válida
	 * Se invoca al iniciar el proceso de login a la instancia
	 *
	 * @param string $id_usuario ID del usuario
	 * @param string $clave Clave o contraseña
	 * @param array $datos_iniciales Información extra del usuario (opcional)
	 * @return boolean
	 */
	static function autenticar($id_usuario, $clave, $datos_iniciales=null);

	/**
	 * ID del usuario
	 * @return string
	 */
	function get_id();

	/**
	 * Nombre del usuario
	 * @return string
	 */
	function get_nombre();
	
	/**
	 *  @return array Perfiles de acceso a los que pertenece el usuario
	 */
	function get_perfiles_funcionales();	

	/**
	 * @return string ID del perfil de datos del usuario actual, null en caso de no poseer
	 */
	function get_perfil_datos();

	/**
	 * return array Restricciones funcionales a las que pertenece el usuario, opcionalmente filtrando por determinados perfiles
	 */
	function get_restricciones_funcionales($perfiles_funcionales = null);

	//-- Manejo de bloqueos --------------------------------------------

	/**
	 * Determina si una IP dada esta bloqueada por el sistema
	 * @param string $ip
	 */
	static function es_ip_rechazada($ip);

	/**
	 * El núcleo informa que un usuario ingreso credenciales incorrectas
	 * @param string $usuario
	 * @param string $ip
	 * @param string $texto Motivo del error
	 */
	static function registrar_error_login($usuario, $ip, $texto);

	/**
	 * El núcleo en base al parámetro de configuración de cantidad de intentos, pide bloquear una IP
	 */
	static function bloquear_ip($ip);

	/**
	 * Retorna la cantidad de intentos fallidos desde una IP en una ventana de tiempo
	 * @param string $ip
	 * @param integer $ventana_temporal Cantidad de minutos
	 * @return integer
	 */
	static function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal=null);

	/**
	 * Retorna la cantidad de intentos fallidos de un usuario en una ventana de tiempo
	 * @param string $usuario
	 * @param integer $ventana_temporal Cantidad de minutos
	 * @return integer
	 */
	static function get_cantidad_intentos_usuario_en_ventana_temporal($usuario, $ventana_temporal=null);


	/**
	 * El núcleo, en base a las configuraciones del proyecto, pide bloquear un usuario
	 * @param string $usuario
	 */
	static function bloquear_usuario($usuario);

	/**
	 * Determina si un usuario dado fue bloqueado
	 * @return boolean
	 */
	static function es_usuario_bloqueado($usuario);

	//------------------------------------------------------------------

	/**
	 * Métodos no usados
	function get_fecha_vencimiento();
	function get_horario_permitido();
	function get_dias_semana_permitidos();
	function get_ip_permitida();
	 */
}
?>