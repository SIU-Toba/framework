<?
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_abm_nav extends objeto_ci
{
	protected $filtro;
	protected $seleccion;
	protected $nueva_entidad=false;
		
	function __construct($id)
	{
		parent::__construct($id);
	}

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_estado_sesion(),"MEMORIA");
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "filtro";
		$estado[] = "seleccion";
		$estado[] = "nueva_entidad";
		return $estado;
	}

	function evt__limpieza_memoria()
	{
		$no_limpiar = isset($this->filtro) ? array("filtro") : null;
		parent::evt__limpieza_memoria($no_limpiar);
	}

	//--------------------------------------------------------------
	//             Construccion de la INTERFACE
	//--------------------------------------------------------------

	function get_etapa_actual()
	//Sobreescribir la navegacion entre etapas
	{
		return ( isset($this->seleccion) || ( isset($this->nueva_entidad) && $this->nueva_entidad) ) ? 2 : 1;
	}
	
	function get_lista_eventos()
	//Generacion de la lista de botones
	{
		$evento = array();
		if( $this->get_etapa_actual() === 1 ){
			//Seleeccion de la entidad
			$evento['agregar']['etiqueta'] = "&Agregar";
			$evento['agregar']['estilo']="abm-input";
			$evento['agregar']['tip']="Agregar un nuevo registro";
		}else{
			//Edicion de la entidad
			$evento['eliminar']['etiqueta'] = "&Eliminar";
			$evento['eliminar']['confirmacion'] = "¿Desea eliminar los datos?";
			$evento['eliminar']['estilo']="abm-input-eliminar";
			$evento['eliminar']['tip']="Eliminar los datos";
			$evento['guardar']['etiqueta'] = "&Guardar";
			$evento['guardar']['estilo']="abm-input";
			$evento['guardar']['tip']="Guardar cambios";
			$evento['cancelar']['etiqueta'] = "&Cancelar";
			$evento['cancelar']['estilo']="abm-input";
			//$evento['cancelar']['confirmacion'] = "¿Desea descartar los cambios?";
			$evento['cancelar']['tip']="Descarta los cambios realizados";
		}
		return $evento;
	}

	function evt__pre_cargar_datos_dependencias()
	{
		if( ($this->get_etapa_actual() === 2) && (isset($this->seleccion)) ){
			$this->dependencias["entidad"]->cargar($this->seleccion);				
		}
	}

	//--------------------------------------------------------------
	//--  EVENTOS Dependencias
	//--------------------------------------------------------------
	
	function evt__filtro__filtrar($datos)
	{
		//if( array_no_nulo($datos) ){
		$this->filtro = $datos;
		//}
	}
	
	function evt__filtro__cancelar($datos)
	{
		unset( $this->filtro );
	}

	function evt__filtro__carga()
	{
		if(isset($this->filtro)){
			return $this->filtro;
		}
	}

	function evt__cuadro__seleccion($registro)
	{
		$this->seleccion = $registro;
	}

	function evt__cuadro__carga()
	{
		$mostrar_cuadro = true;
		if($this->existe_dependencia("filtro")){
			//controlo la definicion
			if(!isset($this->filtro)){
				$mostrar_cuadro = false;
			}
		}
		if($mostrar_cuadro)
		{
			// Cargo los datos del DAO
			if( !isset($this->info["parametro_a"]) || !isset($this->info["parametro_b"]) || !isset($this->info["parametro_c"]) ){
				throw new excepcion_toba("Los parametros del OBJETO estan MAL. Fijarse ejemplo de 'personas'... me fui de vacaciones.");	
			}
			require_once($this->info["parametro_a"]);
			$clase = $this->info["parametro_b"];
			$metodo = $this->info["parametro_c"];
			$x = "\$temp = $clase::$metodo(\$this->filtro);";
			eval($x);
			return $temp;
		}
	}

	//--------------------------------------------------------------
	//-- EVENTOS del CI  -------------------------------------------
	//--------------------------------------------------------------

	function evt__agregar()
	{
		$this->nueva_entidad = true;
	}

	function evt__cancelar()
	{
		$this->disparar_limpieza_memoria();
	}
	
	function evt__guardar()
	{
		$this->dependencias["entidad"]->guardar();
		$this->disparar_limpieza_memoria();
	}
	
	function evt__eliminar()
	{
		$this->dependencias["entidad"]->eliminar();
		$this->disparar_limpieza_memoria();
	}
	//--------------------------------------------------------------

}
?>