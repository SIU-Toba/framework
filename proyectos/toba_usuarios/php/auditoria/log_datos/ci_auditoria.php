<?php 
class ci_auditoria extends toba_ci
{
	protected $s__filtro;
	protected $s__proyecto;
	protected $s__filtrar = false;
	protected $esquema = 'auditoria';
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function ini__operacion()
	{
		if( toba::sesion()->proyecto_esta_predefinido() ) {
			$this->s__proyecto = toba::sesion()->get_id_proyecto();
		}else{
			$this->s__filtro['proyecto'] = toba::sesion()->get_id_proyecto();
		}
	}
	
	function conf()
	{	
		if (isset($this->s__filtro) && isset($this->s__filtro['proyecto'])) {
			$this->s__proyecto = $this->s__filtro['proyecto'];
		}
		if( toba::sesion()->proyecto_esta_predefinido() ) {
			$this->dep('filtro')->desactivar_efs( array('proyecto') );
		}
		if (isset($this->s__proyecto)) {
			$this->esquema = $this->s__proyecto.'_auditoria';			
			$tablas = $this->get_tablas();
			if (empty($tablas)) {
				$desc = 'El proyecto <strong>'.$this->s__proyecto.'</strong> no tiene creado un [wiki:Referencia/Auditoria esquema de auditoría]. ';
				$desc .= "Para crearlo ejecute: <pre>toba proyecto crear_auditoria -p {$this->s__proyecto}";	
				$this->pantalla()->set_descripcion($desc, 'error');				
				$this->pantalla()->eliminar_dep('filtro');
			} else {
				$desc = 'Auditoría del proyecto <strong>'.$this->s__proyecto.'</strong>';	
				$this->pantalla()->set_descripcion($desc);
			}	
		}		
	}
	
	function get_db()
	{
		$id = toba_info_editores::get_fuente_datos_defecto($this->s__proyecto);
		$fuente_datos = toba_admin_fuentes::instancia()->get_fuente( $id,
																	 $this->s__proyecto );
		return $fuente_datos->get_db();
	}	
	
	function get_lista_usuarios()
	{
		return toba_info_permisos::get_lista_usuarios($this->s__proyecto);
	}
	
	
	function get_tablas()
	{
		return $this->get_db()->get_lista_tablas(false, $this->esquema);
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
		$campos_propios = toba_auditoria_tablas_postgres::get_campos_propios();
		list($proyecto, $id_cuadro) = $this->dep('cuadro')->get_id();
		if ($this->s__filtrar && isset($this->s__filtro)) {
			$this->dep('filtro')->colapsar();
			$db = $this->get_db();
			$auditoria = new toba_auditoria_tablas_postgres($db);
			$schema = $db->get_schema();
			if (isset($schema)) {
				$auditoria->set_esquema_origen($schema);			
			}
			$auditoria->set_esquema_logs($this->esquema);
			//--- Se recorre cada tabla buscada y se crea dinamicamente un cuadro
			foreach($this->s__filtro['tablas'] as $tabla) { 
				$datos = $auditoria->get_datos($tabla, $this->s__filtro);
				$claves = $auditoria->get_campos_claves($tabla);
				$this->analizar_diferencias($datos, $claves, $campos_propios);				
				
				$definicion = $this->get_db()->get_definicion_columnas($tabla, $this->esquema);
				foreach ($definicion as $id => $campo) {
					$definicion[$id]['clave'] = $campo['nombre'];
					$definicion[$id]['titulo'] = ucwords(str_replace(array('_', '_'), ' ', $campo['nombre']));
					$definicion[$id]['usar_vinculo'] = false;
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
		}
	}
	
	function analizar_diferencias(& $datos, $campos_clave, $campos_propios)
	{
		$indice = array();
		foreach($datos as $id_fila => $fila) {
			//-- Se indexa el registro actual
			$claves = array();
			foreach($campos_clave as $clave) {
				$claves[] = $fila[$clave];
			}
			$hash_fila = implode(apex_qs_separador, $claves);
			if (isset($indice[$hash_fila])) {
				//--- Existe un registro anterior con esta misma clave?
				$fila_anterior = end($indice[$hash_fila]);		
			} else {
				unset($fila_anterior);
			}
			foreach($fila as $campo => $valor) {
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