<?php

class toba_catalogo_objetos
{
	protected $proyecto;
	protected $objetos;
	
	protected $explicaciones = array();		//Explicaciones de porque entro este objeto
	
	function __construct($proyecto)
	{
		$this->proyecto = $proyecto;
	}
	
	function get_objetos($opciones, $en_profundidad = false, $excluir_clases=array())
	{
		//---Metodo de Consulta (DAO)
		$filtro_dao = "";
		if (isset($opciones['dao']) && $opciones['dao'] != '') {
			$filtro_dao = $this->formar_filtro_dao($opciones['dao']);	
			if (! isset($filtro_dao)) {
				return array();	
			}
		}
		//---Clase
		if (isset($opciones['clase'])) {
			$clases = array($opciones['clase']);	
		} else {
			$clases = toba_info_editores::get_lista_tipo_componentes();
			if(count($excluir_clases)>0) {
				foreach(array_keys($clases) as $i) {
					if(in_array($clases[$i], $excluir_clases)) {
						unset($clases[$i]);
					}
				}
			}
		}
		
		//---ID
		$filtro_id = isset($opciones['id']) ? 'AND	o.objeto = '.quote($opciones['id']) : '';
		
		//---INDICE
		$filtro_indice = isset($opciones['indice']) ? 'AND	o.identificador = '.quote($opciones['indice']) : '';

		//----Extensiones
		$filtro_ext = "";
		if (isset($opciones['extendidos'])) {
			if ($opciones['extendidos'] == 'SI') {
				$filtro_ext = "AND		o.subclase IS NOT NULL";
				if (isset($opciones['subclase'])) {
					$sub_sana = toba_contexto_info::get_db()->quote("%{$opciones['subclase']}%");
					$filtro_ext .= "\nAND o.subclase ILIKE $sub_sana ";
				}				
			} else {
				$filtro_ext = "AND		o.subclase IS NULL";
			}
		}
		
		//---Huerfanos
		$filtro_huerfano = "";
		$proyecto = toba_contexto_info::get_db()->quote($this->proyecto);
		if (isset($opciones['huerfanos']) && $opciones['huerfanos'] == 1) {
			$filtro_huerfano = "AND		o.objeto NOT IN (SELECT objeto FROM apex_item_objeto WHERE proyecto = $proyecto)";
			$filtro_huerfano .= "AND	o.objeto NOT IN (SELECT objeto_proveedor FROM apex_objeto_dependencias WHERE proyecto = $proyecto)";
		}
		
		//---Nombre
		$filtro_nombre = "";
		if (isset($opciones['nombre']) && $opciones['nombre'] != '') {
			$filtro_nombre =  "AND		o.nombre ILIKE '%{$opciones['nombre']}%'";
		}
		
		//---Tabla
		$filtro_tabla = "";
		if (isset($opciones['tabla']) && $opciones['tabla'] != '') {
			$tabla_sana = toba_contexto_info::get_db()->quote("%{$opciones['tabla']}%");
			$subselect = "
				SELECT 
					objeto,
					objeto_proyecto
				FROM apex_objeto_db_registros
				WHERE 
					objeto_proyecto = $proyecto
					AND tabla ILIKE $tabla_sana";
			$filtro_tabla = " AND (o.objeto, o.proyecto) IN ($subselect)";
		}
		
		//-- Se utiliza como sql básica aquella que brinda la definición de un componente
		toba_componente_def::set_db(toba_contexto_info::get_db());
		$sql_base = toba_componente_def::get_vista_extendida($this->proyecto);
		$sql = $sql_base['_info']['sql'];
		$extras = " o.clase IN ('" . implode("', '", $clases) . "')
				AND 	o.proyecto = $proyecto
				$filtro_dao				
				$filtro_id
				$filtro_indice
				$filtro_ext
				$filtro_huerfano
				$filtro_nombre
				$filtro_tabla
		";

		$sql = sql_concatenar_where($sql, array($extras));
		$sql = sql_agregar_ordenamiento($sql, array(array('o.nombre', 'asc')));
		//--- Recorrido
		toba_componente_def::set_db(toba_contexto_info::get_db());
		$datos = toba_contexto_info::get_db()->consultar($sql);
		$this->objetos = array();
		foreach ($datos as $dato) {
			$agregar = true;
			if (isset($opciones['extensiones_rotas']) && $opciones['extensiones_rotas'] == 1) {
				$agregar = $this->tiene_extension_rota($dato);
			}
			if ($agregar) {
				$clave = array('componente' =>$dato['objeto'], 'proyecto' => $this->proyecto);			
				if (! $en_profundidad) {
					$info = toba_constructor::get_info($clave, $dato['clase'], false, 
										array('_info' =>$dato));
				} else {
					$info = toba_constructor::get_info($clave, $dato['clase'], true, null, true); 
				}
				if (isset($this->explicaciones[$dato['objeto']] )) {
					$explicacion = implode("<hr />", $this->explicaciones[$dato['objeto']]);
					$info->set_info_extra($explicacion);
				}
				$this->objetos[] = $info;
				
			}
		}
		return $this->objetos;
	}
	
	
	protected function tiene_extension_rota($dato)
	{
		$archivo = $dato['subclase_archivo'];
		if (isset($dato['punto_montaje'])) {
			$pm = toba_modelo_pms::get_pm($dato['punto_montaje'], toba_editor::get_proyecto_cargado());
			$path_proy = $pm->get_path_absoluto() . '/' . $archivo;
		} else {
			$path_proy = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/".$archivo;
		}
		
		$path_toba = toba_dir()."/php/".$archivo;
		if (!file_exists($path_proy) && !file_exists($path_toba)) {
			$this->explicaciones[$dato['objeto']][] = "Extensión Rota, no se encuentra el archivo <em>$archivo</em>";
			return true;	
		}
		//-- Tiene un AP?
		if ($dato['ap_archivo'] != '') {
			$archivo = $dato['ap_archivo'];
			if (isset($dato['ap_punto_montaje'])) {
				$pm = toba_modelo_pms::get_pm($dato['ap_punto_montaje'], toba_editor::get_proyecto_cargado());
				$path_proy = $pm->get_path_absoluto() . '/' . $archivo;
			} else {
				$path_proy = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/".$archivo;
			}
			$path_toba = toba_dir()."/php/".$archivo;
			if (!file_exists($path_proy) && !file_exists($path_toba)) {
				$this->explicaciones[$dato['objeto']][] = "Extensión del AP Rota, no se encuentra el archivo <em>$archivo</em>";
				return true;	
			}
		}
		return false;
	}
	
	/**
	 * Consulta antes los objetos que contienen una determinada tabla
	 */
	protected function formar_filtro_tabla($tabla)
	{
		
	}
	
	/**
	 * Consulta antes los objetos porque tiene que formar la 'explicacion' 
	 * de porque se filtran estos registros. Con un subselect esto no sería posible
	 */
	protected function formar_filtro_dao($busca)
	{
		$daos_efs = array();
		$daos_efs['clase'] = "el NOMBRE de la CLASE";
		$daos_efs['dao'] = "el NOMBRE del METODO";
		$daos_efs['include'] = "el INCLUDE de la CLASE";
		$daos_efs['agrupador_dao'] = "el NOMBRE del METODO AGRUPADOR";
		$daos_efs['agrupador_clase'] = "el NOMBRE de la CLASE AGRUPADORA";
		$daos_efs['agrupador_include'] = "el INCLUDE de la CLASE AGRUPADORA";		
		$sql_efs = "";
		foreach (array_keys($daos_efs) as $clave) {
			$cond = toba_contexto_info::get_db()->quote("%$clave:%$busca%;");
			$sql_efs .= "ef.inicializacion ILIKE $cond OR ";
		}
		$busca_sano = toba_contexto_info::get_db()->quote("%$busca%");
		$proyecto = toba_contexto_info::get_db()->quote($this->proyecto);
		$sql = "
			SELECT 
				o.objeto,
				ef.inicializacion,
				ef.identificador,
				c.dao_metodo,
				c.dao_nucleo
			FROM apex_objeto o
				LEFT OUTER JOIN apex_objeto_cuadro c ON 
					(c.objeto_cuadro = o.objeto 
					AND c.objeto_cuadro_proyecto = o.proyecto)
				LEFT OUTER JOIN apex_objeto_ei_formulario_ef ef ON
					(ef.objeto_ei_formulario = o.objeto
					AND ef.objeto_ei_formulario_proyecto = o.proyecto)
			WHERE
				o.proyecto = $proyecto
				AND (
					$sql_efs
					c.dao_nucleo ILIKE $busca_sano  OR c.dao_metodo ILIKE $busca_sano
					)
		";
		$rs = toba_contexto_info::get_db()->consultar($sql);
		if (!empty($rs)) {
			$sql = "AND		o.objeto IN (";
			foreach ($rs as $obj) {
				$this->explicaciones[$obj['objeto']][] = $this->formar_explicacion_dao($busca, $obj, $daos_efs);
				$sql .= $obj['objeto'].", ";
			}
			$sql = substr($sql, 0, -2);
			$sql .= ")";
			return $sql;
		}
	}
	
	protected function formar_explicacion_dao($buscado, $datos, $daos_efs)
	{
		if (isset($datos['inicializacion'])) {
			$expl ="";
			foreach ($daos_efs as $clave => $descripcion) {
				$exp_reg = "/$clave:(.*)$buscado(.*);/";
				if (preg_match($exp_reg, $datos['inicializacion'], $res)) {
					$expl .= " se encontró en $descripcion ({$res[1]}<b>$buscado</b>{$res[2]}) de la consulta php.";
				}
			}
			if ($expl == '') {
				$expl = $datos['inicializacion'];	
			}
			return "En el ef <b>{$datos['identificador']}</b>:$expl";;
		}
		if (isset($datos['dao_metodo'])) {
			if (preg_match("/$buscado/i", $datos['dao_metodo'])) {
				return "<b>$buscado</b> se encontró como método <b>{$datos['dao_metodo']}</b> de la consulta php de este cuadro.";
			}
		}
		if (isset($datos['dao_nucleo'])) {
			if (preg_match("/$buscado/i", $datos['dao_nucleo'])) {
				return "<b>$buscado</b> se encontró en el nombre <b>{$datos['dao_nucleo']}</b> de la consulta php de este cuadro.";
			}
		}		
	}
	
}


?>