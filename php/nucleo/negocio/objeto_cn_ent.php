<?php
require_once("nucleo/negocio/objeto_cn_t.php");	//Ancestro de todos los OE

class objeto_cn_ent extends objeto_cn_t
{
	protected $entidad;
	protected $entidad_id;
	protected $seleccion;	

	function __construct($id, $resetear=false)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::__construct($id, $resetear);
	}

	function mantener_estado_sesion()
	//Propiedades que necesitan persistirse en la sesion
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion";
		$propiedades[] = "entidad_id";
		return $propiedades;
	}

	function info_entidad()
	{
		//Informacion
		ei_arbol( $this->entidad->info(), "ENTIDAD");		
		ei_arbol( $this->seleccion, "SELECCION" );
	}

	//-------------------------------------------------------------------------------
	//----- ACCESO a las ENTIDADES
	//-------------------------------------------------------------------------------

	public function ins($datos, $parametros)
	{
		if(!is_array($parametros)){
			throw new excepcion_toba("La funcion generica de acceso a entidades requiere la definicion
			de un array de parametros en el ruteo de eventos del CI");			
		}
		//Identifico el elemento y actualizo la entidad
		$elemento = $parametros[0];
		try{
			$this->entidad->acc_elemento($elemento, "ins", $datos);			
		}catch(excepcion_toba $e){
			$this->informar_msg($e->getMessage(),"info");
		}
	}
	//-------------------------------------------------------------------------------
	
	public function del($parametros)
	{
		if(!is_array($parametros)){
			$this->informar_msg("Error INTERNO","error");
			throw new excepcion_toba("La funcion generica de acceso a entidades requiere la " .
							" definicion de un array de parametros en el ruteo de eventos del CI");			
		}
		$elemento = $parametros[0];
		if(!isset($this->seleccion[$elemento])){
			$this->informar_msg("Error INTERNO","error");
			throw new excepcion_toba("No existe un marcador del registro a eliminar");			
		}
		try{
			$this->entidad->acc_elemento($elemento, "del", $this->seleccion[$elemento]);
			unset($this->seleccion[$elemento]);	
		}catch(excepcion_toba $e){
			$this->informar_msg($e->getMessage(),"info");
		}
	}
	//-------------------------------------------------------------------------------
	
	public function upd($datos, $parametros)
	{
		if(!is_array($parametros)){
			$this->informar_msg("Error INTERNO","error");
			throw new excepcion_toba("La funcion generica de acceso a entidades requiere la " .
							" definicion de un array de parametros en el ruteo de eventos del CI");			
		}
		$elemento = $parametros[0];
		if(!isset($this->seleccion[$elemento])){
			$this->informar_msg("Error INTERNO","error");
			throw new excepcion_toba("No existe un marcador del registro a modificar");			
		}
		$temp['id'] = $this->seleccion[$elemento];
		$temp['registro'] = $datos;
		try{
			$this->entidad->acc_elemento($elemento, "upd", $temp);
			unset($this->seleccion[$elemento]);	
		}catch(excepcion_toba $e){
			$this->informar_msg($e->getMessage(),"info");
		}
	}
	//-------------------------------------------------------------------------------
	
	public function set($datos, $parametros)
	{
		if(!is_array($parametros)){
			throw new excepcion_toba("La funcion generica de acceso a entidades requiere la " .
							" definicion de un array de parametros en el ruteo de eventos del CI");			
		}
		$elemento = $parametros[0];
		try{
			$this->entidad->acc_elemento($elemento, "set", $datos);
		}catch(excepcion_toba $e){
			$this->informar_msg($e->getMessage(),"info");
		}
	}
	//-------------------------------------------------------------------------------
	
	public function get($parametros)
	{
		if(!is_array($parametros)){
			throw new excepcion_toba("La funcion generica de acceso a entidades requiere la " .
							" definicion de un array de parametros en el ruteo de eventos del CI");			
		}
		$elemento = $parametros[0];
		return $this->entidad->acc_elemento($elemento, "get", null);
	}
	//-------------------------------------------------------------------------------
	
	public function get_x($parametros)
	{
		if(!is_array($parametros)){
			throw new excepcion_toba("La funcion generica de acceso a entidades requiere la " .
							" definicion de un array de parametros en el ruteo de eventos del CI");			
		}
		$elemento = $parametros[0];
		if(isset($this->seleccion[$elemento])){
			return $this->entidad->acc_elemento($elemento, "get_x", $this->seleccion[$elemento]);						
		}else{
			return null;
		}
	}
	//-------------------------------------------------------------------------------
		
	public function seleccionar($id_registro, $parametros)
	//Mantiene estado de pantalla de seleccion de registros de la pantalla
	{
		//ei_arbol($datos,"DATOS");
		//ei_arbol($parametros,"PARAMETROS");
		if(!is_array($parametros)){
			throw new excepcion_toba("La funcion generica de mantenimiento de marcadores " .
			" necesita definir el elemento de la entidad a mantener.");
		}
		//Existe el elemento de la ENTIDAD?
		$elemento = $parametros[0];
		if( $this->entidad->existe_elemento($elemento)){
			$this->seleccion[$elemento] = $id_registro;
		}else{
			//Llamada al LOGGER
			echo ei_mensaje("Error en la DEFINCION en la seleccion: el elemento no existe");			
		}
	}
	//-------------------------------------------------------------------------------

	public function limpiar($parametros)
	//Mantiene estado de pantalla de seleccion de registros de la pantalla
	{
		//ei_arbol($datos,"DATOS");
		//ei_arbol($parametros,"PARAMETROS");
		if(!is_array($parametros)){
			throw new excepcion_toba("La funcion generica de mantenimiento de marcadores " .
			" necesita definir el elemento de la entidad a mantener.");
		}
		//Existe el elemento de la ENTIDAD?
		$elemento = $parametros[0];
		unset($this->seleccion[$elemento]);
	}
	//-------------------------------------------------------------------------------
	
	function procesar_especifico()
	{
		$this->entidad->sincronizar_db();
	}

	//-------------------------------------------------------------------------------
	//----- Carga y descarga de entidades
	//-------------------------------------------------------------------------------

	function cargar($id)
	{
		$this->entidad_id = $id;
		$this->entidad->cargar($id);
	}
	
	function existe_entidad_cargada()
	{
		return isset($this->entidad_id);	
	}
	
	function descargar()
	{
		$this->entidad_id = null;
		$this->entidad->descargar();	
	}
	
	//-------------------------------------------------------------------------------

}
?>
