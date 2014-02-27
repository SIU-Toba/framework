<?php 

interface toba_autenticable
{
	/**
	 * @param $id_usuario
	 * @param $clave
	 * @param $datos_iniciales Opcional
	 * @return boolean true si es posible autenticar
	 */
	function autenticar($id_usuario, $clave, $datos_iniciales=null);

	function verificar_clave_vencida($id_usuario);
	
	/**
	 * Verifica en cada pedido de pagina que el usuario actual siga logueado (si aplica al metodo de autenticacion)
	 */
	function verificar_logout();
}

?>