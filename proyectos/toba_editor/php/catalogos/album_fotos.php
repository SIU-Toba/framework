<?php

class album_fotos
{
	protected $tipo;
	
	public function __construct($tipo_fotos) 
	{
		$this->tipo = $tipo_fotos;
	}

	public function agregar_foto($nombre, $nodos_visibles, $opciones, $pred = false)
	{
		abrir_transaccion();
		$this->borrar_foto($nombre);	//Lo borra antes para poder hacer una especie de update
		$nodos_visibles = addslashes(serialize($nodos_visibles));
		$opciones = addslashes(serialize($opciones));
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::get_hilo()->obtener_usuario();
		$es_pred = ($pred) ? "1" : "0";
		$sql = "INSERT INTO apex_admin_album_fotos
					 (proyecto, usuario, foto_nombre, foto_nodos_visibles, foto_opciones, foto_tipo, predeterminada) VALUES
					('$proyecto', '$usuario', '$nombre', '$nodos_visibles', '$opciones', '{$this->tipo}', $es_pred)";
		ejecutar_fuente($sql);
		cerrar_transaccion();
	}
	
	public function cambiar_predeterminada($nombre)
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::get_hilo()->obtener_usuario();		
		
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
		toba::get_db()->ejecutar($sql);
		
		//Actualiza la nueva predeterminada
		$sql = "UPDATE apex_admin_album_fotos 
				SET predeterminada = 1 - COALESCE(predeterminada, 0)
				WHERE 
					proyecto = '$proyecto' AND
					usuario = '$usuario' AND
					foto_tipo = '{$this->tipo}' AND
					foto_nombre = '$nombre'
		";
		toba::get_db()->ejecutar($sql);
		cerrar_transaccion('instancia');
	}
	
	public function get_predeterminada()
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "SELECT 
					foto_nombre
				FROM apex_admin_album_fotos fotos
				WHERE 
					fotos.proyecto = '$proyecto' AND
					fotos.usuario = '$usuario' AND
					fotos.foto_tipo = '{$this->tipo}' AND
					fotos.predeterminada = 1
			";
		$res = toba::get_db()->consultar($sql);
		if (empty($res)) {
			return false;	
		} else {
			return $res[0]['foto_nombre'];	
		}
	}
	
	public function borrar_foto($nombre)
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "DELETE FROM apex_admin_album_fotos
				WHERE
					proyecto = '$proyecto' AND
					usuario = '$usuario' AND
					foto_nombre = '$nombre' AND
					foto_tipo = '{$this->tipo}'
				";
		toba::get_db()->ejecutar($sql);
	}
	
	public function fotos($nombre = null)
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		$usuario = toba::get_hilo()->obtener_usuario();
		$where_nombre = '';
		if ($nombre !== null) {
			$where_nombre = " AND fotos.foto_nombre = '$nombre' ";
		}
		$sql = "SELECT 
					foto_nombre, 
					foto_nodos_visibles,
					foto_opciones,
					predeterminada
				FROM apex_admin_album_fotos fotos
				WHERE 
					fotos.proyecto = '$proyecto' AND
					fotos.usuario = '$usuario' AND
					fotos.foto_tipo = '{$this->tipo}'
					$where_nombre
					AND fotos.foto_nombre != '".apex_foto_inicial."'
			";
		$fotos_en_crudo = toba::get_db()->consultar($sql);
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
	
	public function foto($nombre)
	{
		$fotos = $this->fotos($nombre);
		if (count($fotos) == 0)
			return false;
		else
			return $fotos[0];
	}
}

?>