<?
require_once("nucleo/browser/clases/objeto_ci.php");
/*
	Se necesitan distintos comportamientos de filtrado

*/
class objeto_ci_abm extends objeto_ci
{
	protected $filtro;
	protected $seleccion;
	protected $dbr;
		
	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		if($this->existe_dependencia("filtro")) $estado[] = "filtro";
		$estado[] = "seleccion";
		return $estado;
	}

	function obtener_dbr()
	{
		if (! isset($this->dbr)) {
			$this->cargar_dependencia("datos");
			$this->dbr = $this->dependencias["datos"];
		}
		return $this->dbr;
	}
		
	function evt__limpieza_memoria()
	{
		$no_limpiar = isset($this->filtro) ? array("filtro") : null;
		parent::evt__limpieza_memoria($no_limpiar);
		$dbr = $this->obtener_dbr();
		$dbr->resetear();
	}

	//--------------------------------------------------------------
	//--  EVENTOS Filtro
	//--------------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->filtro = $datos;
	}
	
	function evt__filtro__cancelar()
	{
		unset( $this->filtro );
	}

	function evt__filtro__carga()
	{
		if(isset($this->filtro)){
			//ei_arbol($this->filtro);
			return $this->filtro;
		}
	}

	//--------------------------------------------------------------
	//--  EVENTOS Cuadro
	//--------------------------------------------------------------

	function evt__cuadro__carga()
	{
		$mostrar_cuadro = true;
		if($this->existe_dependencia("filtro")){
			if(!isset($this->filtro)){
				$mostrar_cuadro = false;
			}
		}
		if($mostrar_cuadro){

			require_once($this->info["parametro_a"]);
			$clase = $this->info["parametro_b"];
			$metodo = $this->info["parametro_c"];
			
			if(isset($this->filtro)){
				$x = "\$temp = $clase::$metodo(\$this->filtro);";
			}else{
				$x = "\$temp = $clase::$metodo();";
			}
			eval($x);
			return $temp;
		}
	}

	function evt__cuadro__seleccion($id)
	{
		$this->seleccion = $id;
	}

	//--------------------------------------------------------------
	//--  EVENTOS Formulario
	//--------------------------------------------------------------
	
	function deseleccionar_registro()
	{
		$dbr = $this->obtener_dbr();
		unset($this->seleccion);
		$this->dependencias["cuadro"]->deseleccionar();				
		$dbr->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->deseleccionar_registro();
	}	
	
	function evt__formulario__carga()
	{
		if (isset($this->seleccion)) {
			$dbr = $this->obtener_dbr();
			$dbr->cargar_datos_clave($this->seleccion);
			return $dbr->get_registro(0);
		}
	}
	
	function evt__formulario__alta($registro)
	{
		$dbr = $this->obtener_dbr();
		$dbr->set($registro);
		$dbr->sincronizar();
		$dbr->resetear();
	}
	
	function evt__formulario__modificacion($registro)
	{
		$dbr = $this->obtener_dbr();
		$dbr->set($registro);
		$dbr->sincronizar();
		$this->deseleccionar_registro();
	}

	function evt__formulario__baja()
	{
		$dbr = $this->obtener_dbr();
		$dbr->eliminar_registro(0);
		$dbr->sincronizar();
		$this->deseleccionar_registro();
	}
	//--------------------------------------------------------------
}
?>