<?php

class toba_catalogo_items_perfil extends toba_catalogo_items_base 
{
	private $grupo_acceso;
	
	function __construct($proyecto, $grupo_acceso='')
	{
		$this->grupo_acceso = $grupo_acceso;
		parent::__construct($proyecto);
	}
	
	function cargar()
	{
		$sql = "	SELECT 	i.item as item,
							i.proyecto as proyecto,
							nombre,
							carpeta,
							padre,
							descripcion,
							ia.usuario_grupo_acc as acceso
					FROM apex_item i
						LEFT OUTER JOIN apex_usuario_grupo_acc_item ia
							ON i.item = ia.item AND i.proyecto = ia.proyecto
							AND ia.usuario_grupo_acc = '$this->grupo_acceso'
					WHERE 	i.proyecto = '$this->proyecto'					
					ORDER BY i.carpeta, i.orden, i.nombre";
		$rs = toba_contexto_info::get_db()->consultar($sql);
		
		$this->items = array();
		if (!empty($rs)) {
			foreach ($rs as $fila) {
				if ($fila['carpeta']) {
					$obj = new toba_carpeta_perfil( $fila, $this->grupo_acceso );	
				}else{
					$obj = new toba_item_perfil( $fila, $this->grupo_acceso );	
				}				
				
				$this->items[$fila['item']] = $obj;
			}
			$this->carpeta_inicial = '__raiz__';
			$this->mensaje = "";
			$this->ordenar();
			//$this->filtrar();
		}
	}
}

?>