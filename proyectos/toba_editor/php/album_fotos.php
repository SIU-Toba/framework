<?php

class album_fotos
{
	protected $tipo;
	
	function __construct($tipo_fotos) 
	{
		$this->tipo = $tipo_fotos;
	}

	function agregar_foto($nombre, $nodos_visibles, $opciones)
	{
		$this->borrar_foto($nombre);	//Lo borra antes para poder hacer una especie de update
		$nodos_visibles = addslashes(serialize($nodos_visibles));
		$opciones = addslashes(serialize($opciones));
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::usuario()->get_id();
		$sql = "INSERT INTO apex_arbol_items_fotos (proyecto, usuario, foto_nombre, foto_nodos_visibles, foto_opciones) VALUES
					('$proyecto', '$usuario', '$nombre', '$nodos_visibles', '$opciones')";
		toba::db()->ejecutar($sql);
	}
	
	function borrar_foto($nombre)
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::usuario()->get_id();
		$sql = "DELETE FROM apex_arbol_items_fotos
				WHERE
					proyecto = '$proyecto' AND
					usuario = '$usuario' AND
					foto_nombre = '$nombre'
				";
		toba::db()->ejecutar($sql);
	}
	
	function fotos()
	{
		$proyecto = quote(toba_editor::get_proyecto_cargado());
		$usuario = quote(toba::usuario()->get_id());
		$sql = "SELECT 
					foto_nombre, 
					foto_nodos_visibles,
					foto_opciones
				FROM apex_arbol_items_fotos fotos
				WHERE 
					fotos.proyecto = $proyecto AND
					fotos.usuario = $usuario
			";
		toba::db()->ejecutar($sql);
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