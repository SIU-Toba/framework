<?
require_once("nucleo/browser/clases/objeto_ci.php");
require_once("interface_abm.php");

class ci_abm_dbt extends objeto_ci implements interface_abm
{
/*
	PROBLEMAS NO RESUELTOS
	----------------------

		- Hay que llamar a una ventana de eventos del ancestro
		- Hay que hacer que dibuje solo su layout, que no sea necesario crear las etapas en el administrador
*/

	protected $clave;
	protected $selecciones;
	protected $db_tablas;

	function __construct($id)
	{
		parent::__construct($id);
	}
		
	function destruir()
	{
		//ei_arbol($this->get_estado_sesion(),"ESTADO Interno");
		//ei_arbol($this->obtener_dbt()->info());
		parent::destruir();	
	}

	function obtener_dbt()
	{
		if (! isset($this->db_tablas)) {
			include_once( $this->info["parametro_a"]);
			$clase = $this->info['parametro_b'];
			$this->db_tablas = new $clase($this->info['fuente']);
		}
		return $this->db_tablas;
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "db_tablas";
		$estado[] = "clave";
		$estado[] = "selecciones";
		return $estado;
	}

	function evt__limpieza_memoria()
	{
		$this->obtener_dbt()->resetear();
		parent::evt__limpieza_memoria();		
	}

	/**
	*	Retorna la referencia al cuadro actual si es que existe
	**/
	function cuadro()
	{
		foreach ($this->dependencias as $id => $dep) {
			$insp = $this->inspeccionar_dependencia($id);
			if ($insp['tipo_ei'] == 'ei_cuadro') {
				return $dep;
			}
		}
		return null;
	}
	
	//------------------------------------------------------------------------
	//--  traduzco los EVENTOS de la INTERFACE al DB_TABLAS
	//------------------------------------------------------------------------

	private function inspeccionar_dependencia($dependencia)
	{
		$e = explode("__",$dependencia);
		if($e[0] == "c") {
			$dep['tipo_ei'] = "ei_cuadro";
		}elseif( $e[0] == "f") {
			$dep['tipo_ei'] = "ei_formulario";
		}elseif( $e[0] == "ml") {
			$dep['tipo_ei'] = "ei_formulario_ml";
		}
		$dep['elemento'] = $e[1];
		$dep['cantidad_registros'] = $this->obtener_dbt()->elemento($dep['elemento'])->get_tope_registros();
		return $dep;
	}
	
	public function registrar_evento($id, $evento, $parametros=null)
	//Se disparan eventos dentro del nivel actual
	{
		$dep = $this->inspeccionar_dependencia($id);
		if($dep['tipo_ei'] == "ei_cuadro")								//-- Cuadro
		{	
			$this->selecciones[$dep['elemento']] = $parametros;
		}
		elseif($dep['tipo_ei'] == "ei_formulario")						//-- Formulario
		{	
			if($dep['cantidad_registros'] == 1){
				//El elemento maneja un registro
				$this->obtener_dbt()->elemento($dep['elemento'])->set($parametros);	
			}else{
				if($evento=="alta"){
					$this->obtener_dbt()->elemento($dep['elemento'])->agregar_registro( $parametros );	
				}elseif($evento=="cancelar"){
					$this->deseleccionar_cuadro();
					unset($this->selecciones[$dep['elemento']]);
				}else{
					//Hay una seleccion?
					if(isset($this->selecciones[$dep['elemento']])){
						$registro_seleccionado = $this->selecciones[$dep['elemento']];
						if($evento=="modificacion"){
							//Modifico el registro
							$this->obtener_dbt()->elemento($dep['elemento'])->modificar_registro($parametros, $registro_seleccionado);
						}elseif($evento=="baja"){
							//Elimino el registro
							$this->obtener_dbt()->elemento($dep['elemento'])->eliminar_registro( $registro_seleccionado );	
						}
						unset($this->selecciones[$dep['elemento']]);
						$this->deseleccionar_cuadro();
					}else{
						asercion::error();
					}
				}
			}
		}elseif($dep['tipo_ei'] == "ei_formulario_ml")						//-- Formulario
		{	
			$this->obtener_dbt()->elemento($dep['elemento'])->procesar_registros($parametros);
		}
	}

	function proveer_datos_dependencias($id)
	{
		$dep = $this->inspeccionar_dependencia($id);	
		if($dep['tipo_ei'] == "ei_cuadro")								//-- Cuadro
		{	
			//Si hay algo seleccionado, hay que marcarlo en el cuadro
			if (isset($this->selecciones[$dep['elemento']])) {
				$this->dependencias[$id]->seleccionar($this->selecciones[$dep['elemento']]);
			}
			return $this->obtener_dbt()->elemento($dep['elemento'])->obtener_registros();
		}
		elseif($dep['tipo_ei'] == "ei_formulario")						//-- Formulario
		{
			if($dep['cantidad_registros'] == 1){
				return $this->obtener_dbt()->elemento($dep['elemento'])->get();	
			}else{
				//El elemento maneja N registros, si se selecciono uno lo devuelvo
				if(isset($this->selecciones[$dep['elemento']])){
					return $this->obtener_dbt()->elemento($dep['elemento'])->obtener_registro($this->selecciones[$dep['elemento']]);
				}
			}
		}elseif($dep['tipo_ei'] == "ei_formulario_ml")						//-- Formulario ML
		{	
			return  $this->obtener_dbt()->elemento($dep['elemento'])->obtener_registros(null, true);
		}	
	}
	
	function deseleccionar_cuadro()
	{
		$cuadro = $this->cuadro();
		if ($cuadro)
			$cuadro->deseleccionar();	
	}

	//------------------------------------------------------------------------
	//--  Eventos del ABM
	//------------------------------------------------------------------------

	function cargar($clave)
	{
		if(!isset($this->clave)){
			$this->clave = $clave;
			$this->obtener_dbt()->cargar($clave);
		}
	}
	
	function reset()
	{
		unset($this->clave);
		$this->obtener_dbt()->resetear();
	}
	
	function guardar()
	{
		$this->obtener_dbt()->sincronizar();
	}
	
	function eliminar()
	{
		$this->obtener_dbt()->eliminar();
	}
	//------------------------------------------------------------------------
}
?>