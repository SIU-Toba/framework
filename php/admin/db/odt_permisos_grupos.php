<?php
require_once('nucleo/persistencia/objeto_datos_tabla.php'); 
//--------------------------------------------------------------------
class odt_permisos_grupos extends objeto_datos_tabla
{
	function set_grupos($grupos)
	{
		//Es más fácil borrar todo e insertar lo que viene
		$this->eliminar_filas();
		foreach ($grupos as $grupo) {
			$this->nueva_fila(array('usuario_grupo_acc' => $grupo));
		}
	}
	
	function get_grupos()
	{
		$grupos = array();
		$filas = $this->get_filas();
		foreach ($filas as $fila) {
			$grupos[] = $fila['usuario_grupo_acc'];
		}
		return $grupos;	
	}
}

?>