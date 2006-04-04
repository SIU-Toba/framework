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
		$filtro_nombre = isset($opciones['nombre']) ? "AND		o.nombre ILIKE '%{$opciones['nombre']}%'" : '';
		
		$sql_base = componente_toba::get_vista_extendida($this->proyecto);
		$sql = $sql_base['info']['sql'];
		$sql .= "
				AND o.clase IN ('" . implode("', '", $clases) . "')
				AND 	o.proyecto = '$this->proyecto'
				$filtro_id
				$filtro_ext
				$filtro_huerfano
				$filtro_nombre			
	            ORDER BY o.nombre
		";
		$datos = toba::get_db('instancia')->consultar($sql);
		foreach ($datos as $dato) {
			$agregar = true;
			if (isset($opciones['extensiones_rotas']) && $opciones['extensiones_rotas'] == 1) {
				$archivo = $dato['subclase_archivo'];
				$path_proy = toba::get_hilo()->obtener_proyecto_path()."/php/".$archivo;
				$path_toba = toba_dir()."/php/".$archivo;
				if (file_exists($path_proy) || file_exists($path_toba)) {
					//Si se encuentra el archivo la extension no esta rota
					$agregar = false;
				}
			}
			if ($agregar) {
				$clave = array('componente' =>$dato['objeto'], 'proyecto' => $this->proyecto);			
				$this->objetos[] = constructor_toba::get_info($clave, $dato['clase'], false, array('info' =>$dato));
			}
		}
		
		return $this->objetos;
	}
	
	
}


?>