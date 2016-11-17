<?php 
class ci_auditoria extends toba_ci
{
	protected $s__filtro;
	protected $s__filtrar = false;
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	function ini__operacion()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());
		}
	}

	function get_esquema()
	{	
		if (isset($this->s__filtro) && isset($this->s__filtro['esquema']) && $this->s__filtro['esquema']  != '') {
			return  $this->s__filtro['esquema'];
		} else {
			$db = $this->get_db();
			$schema = $db->get_schema();
		}
		
		if (isset($schema)) {
			return $schema.'_auditoria';
		} else {
			return 'public_auditoria';
		}		
	}
	
	function get_db($proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = $this->s__filtro['proyecto'];
		}
		$id = toba_info_editores::get_fuente_datos_defecto($proyecto);
		$fuente_datos = toba_admin_fuentes::instancia()->get_fuente($id, $proyecto);
		return $fuente_datos->get_db();
	}	
	
	function get_lista_usuarios($proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = $this->s__filtro['proyecto'];
		} else {
			$this->s__filtro['proyecto'] = $proyecto;
		}
		return toba::instancia()->get_lista_usuarios($proyecto);
	}
	
	
	function get_tablas($proyecto=null, $esquema=null)
	{
		$tablas = array();
		if (! isset($proyecto)) {
			$proyecto = $this->s__filtro['proyecto'];
		} else {
			$this->s__filtro['proyecto'] = $proyecto;
		}
		if (! isset($esquema)) {
			$esquema = $this->get_esquema();
		}		
		$db = $this->get_db($proyecto);
		if (isset($db)) {
			$tablas = $db->get_lista_tablas(false, $esquema);
		}
		return $tablas;
	}
	
	function get_esquemas_combo($proyecto=null)
	{
		$resultado = array();
		$db = $this->get_db($proyecto);
		$rs = $db->get_lista_schemas_disponibles();
		foreach($rs as $valores) {
			if (stripos($valores['esquema'], '_auditoria') !== false) {
				$resultado[] = array('id' => $valores['esquema']);
			}
		}
		return $resultado;		
	}
	
	//---- filtro -----------------------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
		$this->s__filtrar = true;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
		$this->s__filtrar = false;
	}

	function conf__filtro($filtro)
	{
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}
	
	//---- cuadro -----------------------------------------------------------------------

	function conf__pant_inicial()
	{
		$hay_datos = false;		
		list($proyecto, $id_cuadro) = $this->dep('cuadro')->get_id();
		if ($this->s__filtrar && ! is_null($this->s__filtro['tablas'])) {
			$db = $this->get_db();
			$auditoria = $db->get_manejador_auditoria();
			if (is_null($auditoria)) {
				throw toba_error_db('No existe manejador de auditoria para este motor de bd');
			}
			$clase = get_class($auditoria);
			$campos_propios = call_user_func(array($clase, 'get_campos_propios'));
			$schema = $db->get_schema();
			if (!isset($schema)) {
				$schema = 'public';
			}
			$auditoria->set_esquema_origen($schema);			
			$schema_toba = toba::instancia()->get_schema_db();
			if (isset($schema_toba)) {
				$auditoria->set_esquema_toba($schema_toba);
			}
			$auditoria->set_esquema_logs($this->get_esquema());
			//--- Se recorre cada tabla buscada y se crea dinamicamente un cuadro
			foreach ($this->s__filtro['tablas'] as $tabla) { 
				$datos = $auditoria->get_datos($tabla, $this->s__filtro);
				if (! empty($datos)) {
					$hay_datos = true;
				}
				$claves = $auditoria->get_campos_claves($tabla);
				$this->analizar_diferencias($datos, $claves, $campos_propios);				
				
				$definicion = $this->get_db()->get_definicion_columnas($tabla, $this->get_esquema());
				foreach ($definicion as $id => $campo) {
					$definicion[$id]['clave'] = $campo['nombre'];
					$definicion[$id]['titulo'] = ucwords(str_replace(array('_', '_'), ' ', $campo['nombre']));
					$definicion[$id]['usar_vinculo'] = false;
					//Esto permite sacar el HTML para los estilos de campo modificado
					$definicion[$id]['permitir_html'] = '1';	
					/*$opciones = toba_catalogo_asistentes::get_campo_opciones_para_cuadro($campo['tipo']);
					$definicion[$id] = array_merge($definicion[$id], $opciones);*/
					
					if (in_array($campo['nombre'], $campos_propios)) {
						unset($definicion[$id]);
					}
				}
				$this->agregar_dependencia('cuadro_'.$tabla, $proyecto, $id_cuadro);
				$cuadro = $this->dep('cuadro_'.$tabla);
				$cuadro->agregar_columnas($definicion);
				$cuadro->set_datos($datos);
				$cuadro->set_titulo($tabla. ' ('.count($datos).' Movimientos)');
				//$cuadro->colapsar();
				$this->pantalla()->agregar_dep('cuadro_'.$tabla);
			}
			if (! $hay_datos) {
				$this->pantalla()->set_descripcion('No se encontraron movimientos según el filtro definido');
			} else {
				$this->dep('filtro')->colapsar();			
			}			
		}
	}
	
	function analizar_diferencias(& $datos, $campos_clave, $campos_propios)
	{
		$indice = array();
		foreach ($datos as $id_fila => $fila) {
			//-- Se indexa el registro actual
			$claves = array();
			foreach ($campos_clave as $clave) {
				$claves[] = $fila[$clave];
			}
			$hash_fila = implode(apex_qs_separador, $claves);
			if (isset($indice[$hash_fila])) {
				//--- Existe un registro anterior con esta misma clave?
				$fila_anterior = end($indice[$hash_fila]);		
			} else {
				unset($fila_anterior);
			}
			foreach ($fila as $campo => $valor) {
				if (! in_array($campo, $campos_propios) && isset($fila_anterior)) {
					if ($valor !== $fila_anterior[$campo]) {	//Se produjo un cambio el valor con respecto a su inmediato anterior
						if ($valor == '') {
							$valor = '&nbsp;';
						}
						$datos[$id_fila][$campo] = "<div class='auditoria-cambio-valor'>$valor</div>";
					}
				}				
				if (in_array($campo, $campos_clave)) {
					$claves[] = $valor;
				}
			}
			$indice[$hash_fila][] = $fila;
		}
	}
}

?>