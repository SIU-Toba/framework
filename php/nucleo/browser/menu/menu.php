<?php

abstract class menu
{
	function plantilla_css()
	{
		return "";
	}
	
	abstract function mostrar();
	
	protected function items_de_menu($solo_primer_nivel=false)
	{
		$rest = "";
		if ($solo_primer_nivel) {
			$rest = " AND i.padre = '' ";
		}
		$grupo = toba::get_hilo()->obtener_usuario_grupo_acceso();
		$sql = "SELECT 	i.padre as 		padre,
						i.carpeta as 	carpeta, 
						i.proyecto as	proyecto,
						i.item as 		item,
						i.nombre as 	nombre
				FROM 	apex_item i LEFT OUTER JOIN	apex_usuario_grupo_acc_item u ON
							(	i.item = u.item AND i.proyecto = u.proyecto	)
				WHERE
					(i.menu = 1)
				AND	(u.usuario_grupo_acc = '$grupo' OR i.publico = 1)
				AND (i.item <> '')
				$rest
				AND		(i.proyecto = '".toba::get_hilo()->obtener_proyecto()."')
				ORDER BY i.padre,i.orden;";
		return toba::get_db('instancia')->consultar($sql);
	}	
}



?>