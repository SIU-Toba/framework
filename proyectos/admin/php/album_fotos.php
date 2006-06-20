<?php

class album_fotos
{
	protected $tipo;
	
	public function __construct($tipo_fotos) 
	{
		$this->tipo = $tipo_fotos;
	}

	public function agregar_foto($nombre, $nodos_visibles, $opciones)
	{
		$this->borrar_foto($nombre);	//Lo borra antes para poder hacer una especie de update
		$nodos_visibles = addslashes(serialize($nodos_visibles));
		$opciones = addslashes(serialize($opciones));
		$proyecto = toba::get_hilo()->obtener_proyecto();
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "INSERT INTO apex_arbol_items_fotos (proyecto, usuario, foto_nombre, foto_nodos_visibles, foto_opciones) VALUES
					('$proyecto', '$usuario', '$nombre', '$nodos_visibles', '$opciones')";
		$res = toba::get_db('instancia')->Execute($sql);
		if (!$res) {
			$error = toba::get_db('instancia')->ErrorMsg();
			throw new excepcion_toba("No fue posible guardar la foto.\n$error\n$sql");
		}
	}
	
	public function borrar_foto($nombre)
	{
		$proyecto = toba::get_hilo()->obtener_proyecto();
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "DELETE FROM apex_arbol_items_fotos
				WHERE
					proyecto = '$proyecto' AND
					usuario = '$usuario' AND
					foto_nombre = '$nombre'
				";
		$res = toba::get_db('instancia')->Execute($sql);
		if (!$res) {
			$error = toba::get_db('instancia')->ErrorMsg();
			throw new excepcion_toba("No fue posible borrar la foto.\n$error\n$sql");
		}
	}
	
	public function fotos()
	{
		$proyecto = toba::get_hilo()->obtener_proyecto();
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "SELECT 
					foto_nombre, 
					foto_nodos_visibles,
					foto_opciones
				FROM apex_arbol_items_fotos fotos
				WHERE 
					fotos.proyecto = '$proyecto' AND
					fotos.usuario = '$usuario'
			";
		$res = toba::get_db('instancia')->Execute($sql);
		if (!$res) {
			$error = toba::get_db('instancia')->ErrorMsg();
			throw new excepcion_toba("No fue posible cargar las fotos.\n$error\n$sql");
		}
		$fotos_en_crudo = $res->GetArray();
		$fotos = array();
		foreach ($fotos_en_crudo as $foto) {
			$fotos[] = array('foto_nombre'=> $foto['foto_nombre'],
							 'foto_nodos_visibles'=> unserialize(stripslashes($foto['foto_nodos_visibles'])),
							 'foto_opciones' => unserialize(stripslashes($foto['foto_opciones']))
							);
		}
		return $fotos;
	}	
	
}

?>