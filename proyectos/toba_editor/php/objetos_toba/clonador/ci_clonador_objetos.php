<?php
require_once('objetos_toba/asignador_objetos.php');
//----------------------------------------------------------------
class ci_clonador_objetos extends toba_ci
{
	protected $id_objeto;
	protected $destino;
	protected $nuevo_nombre;
	
	function ini()
	{
		$props = array('id_objeto', 'datos');
		$this->set_propiedades_sesion($props);
	}	
		
	/********************************
	*			DAOS
	*********************************/
	
	static function get_tipos_destino()
	{
		$clase = toba::zona()->get_tipo_componente();
		$datos = toba_info_editores::get_contenedores_validos($clase);
		$destinos = array();
		$a = 0;
		foreach ($datos as $dato) {
			$destinos[$a]['clase'] = $dato;
			$a++;
		}
		return $destinos;
	}

	static function get_objetos_destino($clase=null)
	{
		if (isset($clase)) {
			switch ($clase) {
				case 'toba_item':
					return toba_info_editores::get_lista_items();
				default:
					$tipo = 'componente,'.$clase;
					return toba_info_editores::get_lista_objetos_toba($tipo);
			}
		}
	}
	
	
	/********************************
	*			EVENTOS
	*********************************/
	
	function conf__destino()
	{
		if (! isset($this->datos)) {
			$this->datos = array();
			$this->datos['proyecto'] = toba_editor::get_proyecto_cargado();	
		}
		return $this->datos;
	}
	
	function evt__destino__modificacion($datos)
	{
		$this->datos = $datos;
		if ($datos['con_destino']) {
			if (isset($datos['tipo']) && isset($datos['objeto'])) {
				$this->destino = $datos;
				//Validaciones 
				if ($this->destino['tipo'] == 'toba_ci' || $this->destino['tipo'] == 'toba_datos_relacion') {
					if (!isset($this->destino['id_dependencia'])) {
						throw new toba_error('El identificador es obligatorio');
					}
				}				
				//Se convierten los tipos a los que entiende el asignador
				$tipo = null;
				switch ($this->destino['tipo']) {
					case 'toba_ci':
						if (isset($this->destino['pantalla'])) {
							$tipo = 'toba_ci_pantalla';
						} else {
							$tipo = 'toba_ci';
						}
						break;
					case 'toba_datos_relacion':
						$tipo = 'toba_datos_relacion';
						break;
					default:
						$tipo = $this->destino['tipo'];
				}
				$this->destino['tipo'] = $tipo;
				$this->destino['proyecto'] = toba_editor::get_proyecto_cargado();
			}
		}
	}
	
	function evt__procesar()
	{
		abrir_transaccion('instancia');
		$directorio = false;
		if ($this->datos['con_subclases']) {
			$directorio = $this->datos['carpeta_subclases'];
		}
		list($proyecto_actual, $comp_actual) = toba::zona()->get_editable();
		$id = array('proyecto' => $proyecto_actual, 'componente' => $comp_actual);
		$info = toba_constructor::get_info($id, null, $this->datos['profundidad']);
		$nuevos_datos = array('anexo_nombre' => $this->datos['anexo_nombre']);
		$clon = $info->clonar($nuevos_datos, $directorio, false);
		
		//--- Asignación
		if (isset($this->destino)) {
			$asignador = new asignador_objetos($clon, $this->destino);
			$asignador->asignar();
		}
		cerrar_transaccion('instancia');
		admin_util::redireccionar_a_editor_objeto($clon['proyecto'], $clon['objeto']);
	}
}

?>