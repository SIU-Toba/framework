<?php
require_once('admin/db/dao_editores.php');
require_once('nucleo/componentes/info/interfaces.php');

class catalogo_objetos
{
	protected $proyecto;
	protected $objetos;
	
	function __construct($proyecto)
	{
		$this->proyecto = $proyecto;
	}
	
	function get_objetos($opciones)
	{
		if (isset($opciones['clase'])) {
			$clases = array($opciones['clase']);	
		} else {
			$clases = dao_editores::get_clases_validas();
		}
		$filtro_id = isset($opciones['id']) ? "AND	o.objeto = '{$opciones['id']}'" : '';
/*		$sql = "SELECT 	
					o.objeto 			        as obj_id,
					o.nombre 					as obj_nombre,
					c.clase 					as cla_id,
					c.editor_item   	        as cla_editor,
					c.editor_proyecto			as cla_editor_proyecto,
					c.instanciador_item	        as cla_instanciador,
					c.instanciador_proyecto		as cla_instanciador_proyecto,
					o.subclase					as obj_subclase
				FROM 	
					apex_objeto o,
					apex_proyecto p,
					apex_clase c,
					apex_clase_tipo t,
					apex_fuente_datos f
				WHERE	
					c.clase_tipo = t.clase_tipo
				AND		o.fuente_datos = f.fuente_datos 
				AND 	f.proyecto = p.proyecto
				AND		o.proyecto = p.proyecto
				AND		o.clase = c.clase
				AND		o.clase IN ('" . implode("', '", $clases) . "')
				$filtro_id
				AND 	p.proyecto = '$this->proyecto'
				AND		t.metodologia = 'capas'
	            ORDER BY obj_nombre
		";		*/
		$filtro_ext = "";
		if (isset($opciones['extendidos'])) {
			if ($opciones['extendidos'] == 'SI') {
				$filtro_ext = "AND		o.subclase IS NOT NULL";
			} else {
				$filtro_ext = "AND		o.subclase IS NULL";
			}
		}
		$filtro_huerfano = "";
		if (isset($opciones['huerfanos']) && $opciones['huerfanos'] == 1) {
			$filtro_huerfano = "AND		o.objeto NOT IN (SELECT objeto FROM apex_item_objeto WHERE proyecto = '{$this->proyecto}')";
			$filtro_huerfano .= "AND	o.objeto NOT IN (SELECT objeto_proveedor FROM apex_objeto_dependencias WHERE proyecto = '{$this->proyecto}')";
		}
		$sql = "SELECT 	
					o.objeto 			        as obj_id,
					o.clase 					as cla_id					
				FROM 	
					apex_objeto o
				WHERE	
						o.clase IN ('" . implode("', '", $clases) . "')
				AND 	o.proyecto = '$this->proyecto'
				$filtro_id
				$filtro_ext
				$filtro_huerfano				
	            ORDER BY o.nombre
		";
		$datos = toba::get_db('instancia')->consultar($sql);
		foreach ($datos as $dato) {
			$clave = array('componente' =>$dato['obj_id'], 'proyecto' => $this->proyecto);
			$this->objetos[] = constructor_toba::get_info($clave, $dato['cla_id']);
		}
		return $this->objetos;
	}
	
	
}


?>