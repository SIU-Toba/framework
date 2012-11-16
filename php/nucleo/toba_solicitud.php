<?php
/**
 * Una solicitud es la representación de una operación o item accedida por un usuario en runtime
 * Contiene e instancia a los componentes de la operación
 *
 * Consumir usando toba::solicitud()->
 * @package Centrales
 */
abstract class toba_solicitud
{
	protected $id;								//ID de la solicitud	
	protected $info;							//Propiedeades	de	la	solicitud extraidas de la base
	protected $info_objetos;					//Informacion sobre los	objetos asociados	al	item
	protected $indice_objetos;					//Indice	de	objetos asociados	por CLASE
	protected $objetos = array();				//Objetos standarts asociados	al	ITEM
	protected $objetos_indice_actual = 0;		//Posicion actual	del array de objetos	
	protected $observaciones = array();			//Array de observaciones realizadas	durante la solicitud	
	protected $registrar_db;					//Indica si	se	va	a registrar	la	solicitud
	protected $cronometrar;						//Indica si	se	va	a registrar	el	cronometro de la solicitud	
	protected $log;								//Objeto que mantiene el log de la ejecucion
	protected $registrada = false;
	
	function __construct($item, $usuario)	
	{
		toba::cronometro()->marcar('Inicio Solicitud');
		$this->item = $item;
		$this->usuario = $usuario;

		for($a=0;$a<count($this->info['objetos']);$a++){
			$indice = $this->info['objetos'][$a]["clase"];
			$this->indice_objetos[$indice][]=$a;
			$objetos[] = $this->info['objetos'][$a]["objeto"];	
		}

		$this->id = toba::instancia()->get_id_solicitud();

		//-- Cargo los OBJETOS que se encuentran asociados
		$this->log = toba::logger();

		//---------- LOG de SOlICITUDES --------------------
		//Se debe cronometrar la pagina?
		if(toba::memoria()->usuario_solicita_cronometrar()) {
			$this->cronometrar = true;
		}		
		//-- Identifico si la solicitd se deber registrar
		if (toba::proyecto()->get_parametro('registrar_solicitud') || $this->info['basica']['item_solic_registrar']) {
			$this->registrar_db	= true;
		}
		
		//-- Hago un registro temprano del acceso por si hay algun fatal en medio de la ejecucion
		$this->registro_temprano();
		/*
		//-- Observaciones automaticas? -> en espera a algun requerimiento que le de forma al esquema
		if( $this->info['basica']['item_solic_registrar'] && $this->info['basica']['item_solic_obs_tipo']){
			$tipo = array($this->info['basica']['item_solic_obs_tipo_proyecto'],$this->info['basica']['item_solic_obs_tipo']);
			$this->observar($this->info['basica']['item_solic_observacion'],$tipo);
		}*/

	}
	
	function set_cronometrar($cronometrar)
	{
		$this->cronometrar = $cronometrar;	
	}
	
	/**
	 * Construye un componente y lo mantiene en un slot interno
	 *
	 * @param string $clase Nombre de la clase de componente
	 * @param int $posicion Posición del componente en la operación
	 * @param mixed $parametros
	 * @return int Indice o slot interno donde se almaceno el componente
	 */
	function cargar_objeto($clase,$posicion,$parametros=null)
	{
		//-[1]- El indice	es	valido?
		if(!isset($this->indice_objetos[$clase][$posicion])){	
			$this->observar(array("toba","error"),"SOLICITUD [get_id_objeto]: No EXISTE un OBJETO	asociado	al	indice [$clase][$posicion].",false,true,true);
			return -1;
		}
		$posicion = $this->indice_objetos[$clase][$posicion];	
		$indice = $this->objetos_indice_actual;

		$clave['proyecto'] = $this->info['objetos'][$posicion]['objeto_proyecto'];
		$clave['componente'] = $this->info['objetos'][$posicion]['objeto'];
		$this->objetos[$indice] = toba_constructor::get_runtime( $clave, $clase );
		$this->objetos_indice_actual++;
		return $indice;
	}	
	
	/**
	 * Destruye los componentes asociados a la operación y el hilo
	 */
	function finalizar_objetos()
	{
		//--- Finalizo objetos TOBA ----------
		//echo "Empiezo a finalizar los objetos...<br>";
		for($a=0;$a<count($this->objetos);$a++){
			$this->objetos[$a]->destruir();
		}
		toba_cargador::instancia()->destruir();
		
		//--- Finalizo objetos BASICOS -------
		toba::memoria()->destruir();
		//dump_session();
		// Guardo cronometro
	}	
	
	function guardar_cronometro()
	{
		if ($this->cronometrar) {			
			if (! $this->registrada) {
				$this->registro_temprano();
			}			
			toba::cronometro()->registrar($this->info['basica']['item_proyecto'], $this->id);
		}			
	}

	abstract function procesar();

	//------------------------------------------------------------------------
	//--------------------------  AUDITORIA Y	LOG --------------------------
	//------------------------------------------------------------------------

	function registro_temprano()
	{
		if( $this->registrar_db || $this->cronometrar) {			
			toba::instancia()->registrar_solicitud($this->id, $this->info['basica']['item_proyecto'], 
													$this->info['basica']['item'], $this->get_tipo());
			$this->registrada = true;
		}
	}
	
	function registrar()	
	{
		toba::cronometro()->marcar('Finalizando Solicitud');		
		if (count($this->observaciones) > 0) $this->registrar_db = true;
		if( $this->registrar_db || $this->cronometrar) {
			// Guardo solicitud
			toba::instancia()->actualizar_solicitud_cronometro($this->id, $this->info['basica']['item_proyecto']);
			// Guardo observaciones
			if(count($this->observaciones)>0) {
				for($i=0;$i<count($this->observaciones);$i++) {
					toba::instancia()->registrar_solicitud_observaciones(	$this->info['basica']['item_proyecto'], 
																		$this->id, 
																		$this->observaciones[$i]['tipo'], 
																		$this->observaciones[$i]['observacion'] );
				}
			}
		}
	}

	/**
	 * Permite asociar observaciones al registro de la solicitud actual
	 */
	function observar($observacion, $tipo=null)
	{
		if(!isset($tipo)){
			$tipo = array('toba','info');
		}else{
			$tipo = array(toba::proyecto()->get_id(), $tipo);		
		}
		$this->observaciones[] = array('tipo'=>$tipo,'observacion'=>$observacion);
	}

	//----------------------------------------------------------
	//-------------------------- Consultas --------------------------
	//----------------------------------------------------------

	function get_tipo()
	{
		return $this->info['basica']['item_solic_tipo'];
	}
	
	function es_item_publico()
	{
		return $this->info['basica']['item_publico'];	
	}
	
	function existe_ayuda()	
	{
		return (trim($this->info['basica']['item_existe_ayuda'])!="");	
	}
	
	/**
	* Retorna un arreglo de datos básicos de la operación que se esta ejecutando
	* @param string $prop Propiedad a obtener (opcional)
	*/
	function get_datos_item($prop=null)
	{
		if (isset($prop)) {
			return $this->info['basica'][$prop];	
		}
		return $this->info['basica'];	
	}

	/**
	 * Permite cambiar alguno de los seteos basicos del item en runtime, para que el cambio de alguna
	 * de las propiedades se vea por pantalla puede ser necesario el retraso del envio de Headers
	 * @param array $prop Arreglo asociativo con las propiedades a cambiar ej: array('item_descripcion' => 'No tengo', 'item_nombre' => 'Nuevo nombre')
	 */
	function set_datos_item($prop = array())
	{
		$actual = $this->info['basica'];
		$this->info['basica'] = array_merge($actual, $prop);
	}

	/**
	 * Permite setear el tipo de pagina para la solicitud actual
	 * @param array $tipo Arreglo asociativo con un nombre de clase y archivo, ej: array('clase' => 'toba_tp_mia', ['archivo' => 'customizacion_toba/toba_tp_mia.php']);
	 */
	function set_tipo_pagina($tipo)
	{
		$parametros = array('tipo_pagina_clase' => $tipo['clase']);
		if (isset($tipo['archivo'])) {
			$parametros['tipo_pagina_archivo'] = $tipo['archivo'];
		}
		$this->set_datos_item($parametros);
	}

	/**
	 * Retorna el id de la operacion actual
	 */
	function get_id_operacion()
	{
		return $this->info['basica']['item'];
	}
	
	/**
	 * Retorna un id que representa a todo el pedido de página actual
	 */
	function get_id()
	{
		return $this->id;	
	}
	
	//----------------------------------------------------------
	//-------------------------- ZONA --------------------------
	//----------------------------------------------------------

	protected function crear_zona()
	{
		$clase = 'toba_zona';
		if (trim($this->info['basica']['item_zona'])!="") {
			//--- Tiene subclase?
			if (isset($this->info['basica']['item_zona_archivo'])) {
				$punto = toba::puntos_montaje()->get_por_id($this->info['basica']['zona_punto_montaje']);
				$path  = $punto->get_path_absoluto().'/'.$this->info['basica']['item_zona_archivo'];
				require_once($path);
				$clase = $this->info['basica']['item_zona'];
			}
			//--- Tiene consulta?
			$consulta = array(
					'archivo' => $this->info['basica']['zona_cons_archivo'],
					'clase' => $this->info['basica']['zona_cons_clase'],
					'metodo' => $this->info['basica']['zona_cons_metodo']
			);
			$this->zona = new $clase($this->info['basica']['item_zona'], $consulta);
		}
	}	
	
	/**
	 * @return zona
	 */
	function zona()
	{
		if(isset($this->zona)) {
			return $this->zona;
		} else {
			throw new toba_error_def('ERROR: No existe una zona cargada');	
		}
	}
	
	/**
	 * Hay una zona asignada y creada?
	 * @return boolean
	 */
	function hay_zona()
	{
		return isset($this->zona);	
	}


}
?>