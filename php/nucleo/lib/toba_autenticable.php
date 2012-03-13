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
}

?>