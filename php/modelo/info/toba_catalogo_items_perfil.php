<?php

class toba_catalogo_items_perfil extends toba_catalogo_items_base 
{
	private $grupo_acceso;
	
	function __construct($proyecto=null)
	{
		parent::__construct($proyecto);
	}
	
	function cargar($opciones, $id_item_inicial=null, $incluidos_forzados=array())
	{
		if (! isset($id_item_inicial)) { 
			$id_item_inicial = '__raiz__';	
		}
		$en_profundidad = $this->debe_cargar_en_profundidad($id_item_inicial, $opciones);
		$filtro_items = "";		
		if (!$this->debe_cargar_todo($opciones) || $en_profundidad) {
			//--- Se dejan solo los items del primer nivel, excepto que este en las excepciones
			if (isset($id_item_inicial)) {
				$filtro_padre = "(i.padre = '$id_item_inicial' OR i.item= '$id_item_inicial')";
						//OR i.padre IN (SELECT item FROM apex_item WHERE padre='$id_item_inicial'))";
			}
			
			if (! empty($incluidos_forzados) && !$en_profundidad) {
				$forzados = implode("', '", $incluidos_forzados);
				$filtro_incluidos = "( i.padre IN ('".$forzados."')";
				$filtro_incluidos .= " OR i.item IN ('".$forzados."') )";			
			}
			
			if (isset($filtro_padre) && isset($filtro_incluidos)) {
				$filtro_items ="	AND ($filtro_padre 
										OR 
									$filtro_incluidos)
					";
			} elseif (isset($filtro_padre)) {
				$filtro_items = "	AND $filtro_padre ";	
			} elseif (isset($filtro_incluidos)) {
				$filtro_items = "	AND $filtro_incluidos ";
			}
		}
		
		if (isset($opciones['solo_carpetas']) && $opciones['solo_carpetas'] == 1) {
			$filtro_items .= "	AND i.carpeta = 1";
		}
		
		//-- Se utiliza como sql básica aquella que brinda la definición de un componente
		$sql_base = toba_item_def::get_vista_extendida($this->proyecto);
		$sql = $sql_base['basica']['sql'];
		//$sql = $this->get_vista_item();
		$sql .=	$filtro_items;
		$sql .= "	AND		(i.solicitud_tipo IS NULL OR i.solicitud_tipo <> 'fantasma')";
		$sql .= "	ORDER BY i.carpeta, i.orden, i.nombre";
		$rs = toba_contexto_info::get_db()->consultar($sql);
		$this->items = array();
		if (!empty($rs)) {
			foreach ($rs as $fila) {
				$id = array();
				$id['componente'] = $fila['item'];
				$id['proyecto'] = $fila['item_proyecto'];
				$datos = array('basica' => $fila);
				
				$obj = new toba_item_perfil( $datos, $en_profundidad);
				
				$this->items[$fila['item']] = $obj;
			}
			$this->carpeta_inicial = $id_item_inicial;
			$this->mensaje = "";
			$this->ordenar();
			$this->filtrar($opciones);
		}
	}
	
	function get_vista_item()
	{
		$sql = "SELECT	i.proyecto as							item_proyecto,	
						i.item as								item,	
						i.nombre	as							item_nombre,
						i.descripcion as						item_descripcion,	
						i.actividad_buffer_proyecto as			item_act_buffer_proyecto,
						i.actividad_buffer as					item_act_buffer,	
						i.actividad_patron_proyecto as			item_act_patron_proyecto,
						i.actividad_patron as					item_act_patron,	
						i.actividad_accion as					item_act_accion_script,	
						i.solicitud_tipo as						item_solic_tipo,	
						i.solicitud_registrar as				item_solic_registrar,
						i.solicitud_obs_tipo_proyecto	as		item_solic_obs_tipo_proyecto,	
						i.solicitud_obs_tipo	as				item_solic_obs_tipo,	
						i.solicitud_observacion	as				item_solic_observacion,	
						i.solicitud_registrar_cron	as			item_solic_cronometrar,	
						i.parametro_a as						item_parametro_a,	
						i.parametro_b as						item_parametro_b,	
						i.parametro_c as						item_parametro_c,
						i.imagen_recurso_origen as				item_imagen_recurso_origen,
						i.imagen as								item_imagen,
						pt.clase_nombre	as						tipo_pagina_clase,
						pt.clase_archivo as						tipo_pagina_archivo,
						pt.include_arriba	as					item_include_arriba,	
						pt.include_abajo as						item_include_abajo,	
						i.zona_proyecto as						item_zona_proyecto,
						i.zona as								item_zona,
						z.archivo as							item_zona_archivo,
						z.consulta_archivo as 					zona_cons_archivo,
						z.consulta_clase as						zona_cons_clase,
						z.consulta_metodo as					zona_cons_metodo,
						i.publico as							item_publico,
						ii.item as								item_existe_ayuda,
						i.carpeta as							carpeta,
						i.menu as								menu,
						i.orden as								orden,
						i.publico as							publico,
						i.redirecciona as						redirecciona,
						i.solicitud_registrar_cron as			crono,
						i.solicitud_tipo as						solicitud_tipo,
						i.padre	as 								item_padre,
						
						(SELECT COUNT(*) FROM apex_item_objeto 
							WHERE item = i.item AND proyecto = i.proyecto) as cant_dependencias,
						(SELECT COUNT(*) FROM apex_item 
							WHERE padre = i.item AND proyecto = i.proyecto AND (solicitud_tipo <> 'fantasma' OR solicitud_tipo IS NULL) AND item != '__raiz__') as cant_items_hijos,
						m.molde as molde
				FROM	apex_item i	
							LEFT OUTER JOIN apex_item_zona z	ON	( i.zona_proyecto	= z.proyecto AND i.zona	= z.zona	)
							LEFT OUTER JOIN apex_item_info ii ON (i.proyecto = ii.item_proyecto AND i.item = ii.item)
							LEFT OUTER JOIN	apex_pagina_tipo pt	ON (pt.pagina_tipo	= i.pagina_tipo	AND	pt.proyecto	= i.pagina_tipo_proyecto)
							LEFT OUTER JOIN apex_molde_operacion m ON (i.item = m.item AND i.proyecto = m.proyecto)
							LEFT OUTER JOIN apex_usuario_grupo_acc a ON (i.item = a.item AND i.proyecto = a.proyecto AND a.usuario_grupo_acc = '$this->grupo_acceso')
				WHERE	i.proyecto = '$this->proyecto'";
	}
	
	function set_grupo_acceso($grupo)
	{
		$this->grupo_acceso = $grupo;
	}
	
}

?>