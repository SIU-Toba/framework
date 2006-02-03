<?php

class dao_permisos
{
	static function get_lista_permisos($condiciones = null)
	{
		$where = '';
		if ($condiciones != null) {
			if (isset($condiciones['nombre'])) {
				$where .= "AND	nombre ILIKE '%{$condiciones['nombre']}%' ";
			}
			if (isset($condiciones['descripcion'])) {
				$where .= "AND	descripcion ILIKE '%{$condiciones['descripcion']}%' ";
			}
		}
		$sql = "SELECT 	
					permiso,
					nombre,
					descripcion
				FROM apex_permiso 
				WHERE 
					proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
					$where
				ORDER BY nombre, descripcion
		";
		return consultar_fuente($sql, "instancia");
	}
	
}


?>