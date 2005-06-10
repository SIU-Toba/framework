<?
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_abm_dbr extends objeto_ci
{
	protected $filtro;
	protected $seleccion;
	protected $dbr;
		
	function __construct($id)
	{
		parent::__construct($id);
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "filtro";
		$estado[] = "seleccion";
		return $estado;
	}

	function obtener_dbr()
	{
		if (! isset($this->dbr)) {
			include_once( $this->info["parametro_d"]);
			$clase = $this->info['parametro_e'];
			$this->dbr = new $clase("dbr_".$this->id, $this->info['fuente'], 1, true);
		}
		return $this->dbr;
	}
	
	
	function evt__limpieza_memoria()
	{
		parent::evt__limpieza_memoria(array("filtro"));
	}
/*
	function evt__post_cargar_datos_depedencias()
	{
	}
*/
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
			return $this->filtro;
		}
	}

	//--------------------------------------------------------------
	//--  EVENTOS Cuadro
	//--------------------------------------------------------------

	function evt__cuadro__carga()
	{
		//if(isset($this->filtro)){
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
		//}
	}

	function evt__cuadro__seleccion($id)
	{
		$this->seleccion = $id;
	}

	//--------------------------------------------------------------
	//--  EVENTOS Formulario
	//--------------------------------------------------------------
	
	function evt__formulario__cancelar()
	{
		unset($this->seleccion);
	}	
	
	function evt__formulario__carga()
	{
		if (isset($this->seleccion)) {
			$dbr = $this->obtener_dbr();
			$dbr->cargar_datos_clave($this->seleccion);
			return $dbr->obtener_registro(0);
		}
	}
	
	function evt__formulario__alta($registro)
	{
		$dbr = $this->obtener_dbr();
		$dbr->set($registro);
		$dbr->sincronizar();
	}
	
	function evt__formulario__modificacion($registro)
	{
		$dbr = $this->obtener_dbr();
		$dbr->set($registro);
		$dbr->sincronizar();
	}

	function evt__formulario__baja()
	{
		$dbr = $this->obtener_dbr();
		$dbr->eliminar_registro(0);
		$dbr->sincronizar();
	}
	//--------------------------------------------------------------
}
?>