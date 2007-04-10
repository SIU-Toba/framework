<?php

class odt_permisos_grupos extends toba_datos_tabla
{
	function set_grupos($grupos)
	{
		//Es más fácil borrar todo e insertar lo que viene
		$this->eliminar_filas(false);
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
	
	function get_permisos()
	{
		$permisos = array();
		$filas = $this->get_filas();
		foreach ($filas as $fila) {
			$permisos[] = $fila['permiso'];
		}
		return $permisos;
	}
	
	function set_permisos($nuevos)
	{
		//Es más fácil borrar todo e insertar lo que viene
		$this->eliminar_filas(false);
		foreach ($nuevos as $permiso) {
			$this->nueva_fila(array('permiso'=> $permiso));
		}
	}
}
?>