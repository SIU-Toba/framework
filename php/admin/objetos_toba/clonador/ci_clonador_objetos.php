<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/db/dao_editores.php');
require_once('admin/objetos_toba/clonador_objetos.php');
require_once('admin/objetos_toba/asignador_objetos.php');
require_once('admin/admin_util.php');
//----------------------------------------------------------------
class ci_clonador_objetos extends objeto_ci
{
	protected $id_objeto;
	protected $destino;
	protected $nuevo_nombre;
	
	function __construct($id)
	{
		parent::__construct($id);
		//Cargo el editable de la zona
		$zona = toba::get_solicitud()->zona();
		if (isset($zona) && $editable = $zona->obtener_editable_propagado()){
			$zona->cargar_editable(); 
			list($proyecto, $objeto) = $editable;
		}
		if (isset($objeto) && isset($proyecto)) {
			$this->id_objeto = array('objeto' => $objeto, 'proyecto' => $proyecto);
		}	
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "id_objeto";
		return $propiedades;
	}		
	
	/********************************
	*				DAOS
	*********************************/
	
	static function get_tipos_destino()
	{
		$destinos = array(
						array(
							'proyecto' => toba::get_hilo()->obtener_proyecto(),
							'clase' => 'item'
						)
					);
		$destinos = array_merge($destinos, dao_editores::get_clases_contenedoras());
		//Agregar el item
		return $destinos;
	}

	static function get_objetos_destino($clase=null)
	{
		if (isset($clase)) {
			switch ($clase) {
				case 'item':
					return dao_editores::get_lista_items();
					break;
				default:
					$tipo = "????,".$clase;
					return dao_editores::get_lista_objetos_toba($tipo);
			}
		}
	}
	
	
	/********************************
	*			EVENTOS
	*********************************/
	
	function evt__destino__modificacion($datos)
	{
		$this->nuevo_nombre = $datos['nuevo_nombre'];
		if ($datos['con_destino']) {
			if (isset($datos['tipo']) && isset($datos['objeto'])) {
				$this->destino = $datos;
				//Validaciones 
				if ($this->destino['tipo'] == 'objeto_ci' || $this->destino['tipo'] == 'objeto_ci') {
					if (!isset($this->destino['id_dependencia'])) {
						throw new excepcion_toba("El identificador es obligatorio");
					}
				}				
				//Se convierten los tipos a los que entiende el asignador
				$tipo = null;
				switch ($this->destino['tipo']) {
					case 'objeto_ci':
						if (isset($this->destino['pantalla'])) {
							$tipo = 'ci_pantalla';
						} else {
							$tipo = 'ci';
						}
						break;
					case 'objeto_datos_relacion':
						$tipo = 'datos_relacion';
						break;
					default:
						$tipo = $this->destino['tipo'];
				}
				$this->destino['tipo'] = $tipo;
				$this->destino['proyecto'] = toba::get_hilo()->obtener_proyecto();
			}
		}
	}
	
	function evt__procesar()
	{
		abrir_transaccion("instancia");
		//--- Clonaci�n
		$clonador = new clonador_objetos();
		$clonador->cargar_db(array($this->id_objeto['proyecto'], $this->id_objeto['objeto']));
		$clon = $clonador->clonar($this->nuevo_nombre, false);
		
		//--- Asignaci�n
		if (isset($this->destino)) {
			$asignador = new asignador_objetos($clon, $this->destino);
			$asignador->asignar();
		}
		cerrar_transaccion("instancia");		
		admin_util::redireccionar_a_editor_objeto($clon['proyecto'], $clon['objeto']);
	}
	
	function generar_interface_grafica()
	{
		$zona = toba::get_solicitud()->zona();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->obtener_html_barra_superior();
		}
		parent::generar_interface_grafica();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->obtener_html_barra_inferior();
		}	
	}
	
}

?>