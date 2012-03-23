<?php

class album_fotos
{
	protected $tipo;
	
	function __construct($tipo_fotos) 
	{
		$this->tipo = $tipo_fotos;
	}

	function agregar_foto($nombre, $nodos_visibles, $opciones, $pred=false)
	{
		abrir_transaccion();
		$this->borrar_foto($nombre);	//Lo borra antes para poder hacer una especie de update
		$nodos_visibles = addslashes(serialize($nodos_visibles));
		$opciones = addslashes(serialize($opciones));
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::usuario()->get_id();
		$es_pred = ($pred) ? '1' : '0';
		$sql = "INSERT INTO apex_admin_album_fotos
					 (proyecto, usuario, foto_nombre, foto_nodos_visibles, foto_opciones, foto_tipo, predeterminada) VALUES
					('$proyecto', '$usuario', '$nombre', '$nodos_visibles', '$opciones', '{$this->tipo}', $es_pred)";
		ejecutar_fuente($sql);
		cerrar_transaccion();
	}
	
	function cambiar_predeterminada($nombre)
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::usuario()->get_id();		
		
		abrir_transaccion('instancia');
		//Actualiza las otras fotos
		$sql = "UPDATE apex_admin_album_fotos 
				SET predeterminada = 0 
				WHERE 
					proyecto = '$proyecto' AND
					usuario = '$usuario' AND
					foto_tipo = '{$this->tipo}' AND
					foto_nombre != '$nombre'
		";
		toba::db()->ejecutar($sql);
		
		//Actualiza la nueva predeterminada
		$sql = "UPDATE apex_admin_album_fotos 
				SET predeterminada = 1 - COALESCE(predeterminada, 0)
				WHERE 
					proyecto = '$proyecto' AND
					usuario = '$usuario' AND
					foto_tipo = '{$this->tipo}' AND
					foto_nombre = '$nombre'
		";
		toba::db()->ejecutar($sql);
		cerrar_transaccion('instancia');
	}
	
	function get_predeterminada()
	{
		$proyecto = quote(toba_editor::get_proyecto_cargado());
		$usuario = quote(toba::usuario()->get_id());
		$tipo = quote($this->tipo);		
		$sql = "SELECT 
					foto_nombre
				FROM apex_admin_album_fotos fotos
				WHERE 
					fotos.proyecto = $proyecto AND
					fotos.usuario = $usuario AND
					fotos.foto_tipo = $tipo AND
					fotos.predeterminada = 1
			";
		$res = toba::db()->consultar($sql);
		if (empty($res)) {
			return false;	
		} else {
			return $res[0]['foto_nombre'];	
		}
	}
	
	function borrar_foto($nombre)
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::usuario()->get_id();
		$sql = "DELETE FROM apex_admin_album_fotos
				WHERE
					proyecto = '$proyecto' AND
					usuario = '$usuario' AND
					foto_nombre = '$nombre' AND
					foto_tipo = '{$this->tipo}'
				";
		toba::db()->ejecutar($sql);
	}
	
	function fotos($nombre=null)
	{
		$proyecto = quote(toba_editor::get_proyecto_cargado());
		$usuario = quote(toba::usuario()->get_id());
		$tipo = quote($this->tipo);
		$where_nombre = '';
		if ($nombre !== null) {
			$where_nombre = ' AND fotos.foto_nombre = '. quote($nombre);
		}
		$sql = "SELECT 
					foto_nombre, 
					foto_nodos_visibles,
					foto_opciones,
					predeterminada
				FROM apex_admin_album_fotos fotos
				WHERE 
					fotos.proyecto = $proyecto AND
					fotos.usuario = $usuario AND
					fotos.foto_tipo = $tipo
					$where_nombre
					AND fotos.foto_nombre != '".apex_foto_inicial."'
			";
		$fotos_en_crudo = toba::db()->consultar($sql);
		$fotos = array();
		foreach ($fotos_en_crudo as $foto) {
			$fotos[] = array('foto_nombre'=> $foto['foto_nombre'],
							 'foto_nodos_visibles'=> unserialize(stripslashes($foto['foto_nodos_visibles'])),
							 'foto_opciones' => unserialize(stripslashes($foto['foto_opciones'])),
							 'predeterminada' => $foto['predeterminada']
							);
		}
		return $fotos;
	}	
	
	function foto($nombre)
	{
		$fotos = $this->fotos($nombre);
		if (count($fotos) == 0) {
			return false;
		} else {
			return $fotos[0];
		}
	}
}

?>