<?
require_once("nucleo/browser/clases/objeto_ci_me_tab.php");

class ci_abm_dbt extends objeto_ci_me_tab
{
	protected $clave;
	protected $selecciones;
	private $db_tablas;
		
	function __construct($id)
	{
		parent::__construct($id);
		include_once( $this->info["parametro_a"]);
		$clase = $this->info['parametro_b'];
		$this->db_tablas = new $clase($this->info['fuente']);
	}

	function destruir()
	{
		ei_arbol($this->get_estado_sesion(),"ESTADO Interno");
		//ei_arbol($this->db_tablas->info());
		parent::destruir();	
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "clave";
		$estado[] = "selecciones";
		return $estado;
	}

	//------------------------------------------------------------------------
	//--  traduzco los EVENTOS al DB_TABLAS
	//------------------------------------------------------------------------

	public function registrar_evento($id, $evento, $parametros=null)
	//Se disparan eventos dentro del nivel actual
	{
		$e = explode("_",$id);
		$tipo_ei = $e[0];
		$elemento = $e[1];
		if($tipo_ei == "c"){					//-- Cuadro
			//Los cuadros solo seleccionan
			$this->selecciones[$elemento] = $parametros;
		}elseif($tipo == "f"){					//-- Formulario
			//Obtengo el elemento manejado
			//Obtengo la cantidad de registro que maneja db_registros
			$cr = $this->db_tablas->obtener_cardinalidad();
			if($cr == 1){
				//El elemento maneja un registro
				ei_arbol($parametros, $id . " - " .$evento);
			}else{
				//El elemento maneja mas de un registro
				ei_arbol($parametros, $id . " - " .$evento);
			}
		}
	}

	function proveer_datos_dependencias($dependencia)
	{
		echo "CONTENIDO DE : $dependencia";		
	}

	//------------------------------------------------------------------------
	//--  Eventos del ABM
	//------------------------------------------------------------------------

	function cargar($clave)
	{
		$this->clave = $clave;
		$this->db_tablas->cargar($clave);
	}
	
	function eliminar()
	{
		// Eliminar el DBTABLA
	}
	//------------------------------------------------------------------------
}
?>