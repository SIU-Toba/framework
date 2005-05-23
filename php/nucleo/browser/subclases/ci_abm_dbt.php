<?
require_once("nucleo/browser/clases/objeto_ci_me_tab.php");

class ci_abm_dbt extends objeto_ci_me_tab
{
/*
	FALTA: 
			- Hay que implementar un caso que use filtros
*/

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
		//ei_arbol($this->get_estado_sesion(),"ESTADO Interno");
		ei_arbol($this->db_tablas->info());
		parent::destruir();	
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "clave";
		$estado[] = "selecciones";
		return $estado;
	}

	function evt__limpieza_memoria()
	{
		$this->db_tablas->descargar();
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
		$e = explode("_",$dependencia);
		if($e[0] == "c") {
			$dep['tipo_ei'] = "ei_cuadro";
		}elseif( $e[0] == "f") {
			$dep['tipo_ei'] = "ei_formulario";
		}
		$dep['elemento'] = $e[1];
		$dep['cantidad_registros'] = $this->db_tablas->obtener_cardinalidad($dep['elemento']);
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
				$this->db_tablas->acc_elemento($dep['elemento'],"set",$parametros);	
			}else{
				if($evento=="alta"){
					$this->db_tablas->acc_elemento($dep['elemento'],"ins",$parametros);	
				}elseif($evento=="cancelar"){
					$this->deseleccionar_cuadro();
					unset($this->selecciones[$dep['elemento']]);
				}else{
					//Hay una seleccion?
					if(isset($this->selecciones[$dep['elemento']])){
						if($evento=="modificacion"){
							//Modifico el registro
							$reg['registro'] = $parametros;
							$reg['id'] = $this->selecciones[$dep['elemento']];
							$this->db_tablas->acc_elemento($dep['elemento'],"upd", $reg);
						}elseif($evento=="baja"){
							//Elimino el registro
							$this->db_tablas->acc_elemento($dep['elemento'], "del", $this->selecciones[$dep['elemento']]);	
						}
						unset($this->selecciones[$dep['elemento']]);
						$this->deseleccionar_cuadro();
					}else{
						asercion::error();
					}
				}
			}
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
			return $this->db_tablas->acc_elemento($dep['elemento'],"get");	
		}
		elseif($dep['tipo_ei'] == "ei_formulario")						//-- Formulario
		{
			if($dep['cantidad_registros'] == 1){
				return $this->db_tablas->acc_elemento($dep['elemento'],"get");	
			}else{
				//El elemento maneja N registros, si se selecciono uno lo devuelvo
				if(isset($this->selecciones[$dep['elemento']])){
					return $this->db_tablas->acc_elemento($dep['elemento'],"get_x",$this->selecciones[$dep['elemento']]);
				}
			}
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
		$this->clave = $clave;
		$this->db_tablas->cargar($clave);
	}
	
	function guardar()
	{
		$this->db_tablas->sincronizar_db();
	}
	
	function eliminar()
	{
		$this->db_tablas->eliminar();
	}
	//------------------------------------------------------------------------
}
?>
