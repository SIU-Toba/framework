<?php
require_once("nucleo/browser/clases/objeto_ci.php");
/*
	Relaciona un CI con un CN
*/
class ci_cn extends objeto_ci
{
	protected $cn;

	function __construct($id)
	{
		parent::__construct($id);
	}

	function asignar_controlador_negocio( $controlador )
	{
		$this->cn = $controlador;
	}

	function inicializar_dependencia($dep, $parametro)
	{
		if($this->dependencias[$dep] instanceof objeto_ci ){
			/*
				ATENCION: 	Este metodo tiene que ser solicitado a demanda,
							no es tan natural que siempre los arboles de CIs
							trabajen con un solo CN.
							Para poder hacer esto tiene que haber un metodo de 
							comunicacion establecido
			
			*/
			$this->dependencias[$dep]->asignar_controlador_negocio( $this->cn );
		}
		parent::inicializar_dependencia($dep, $parametro);
	}

	//-----------------------------------------------------------
	//-----------------------------------------------------------
	//---------  Entrega y Salida de datos al CN
	//-----------------------------------------------------------
	//-----------------------------------------------------------

	//--  ENTRADA  ----

	function disparar_obtencion_datos_cn( $modo=null )
	{
		$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ]");
		$this->evt__obtener_datos_cn( $modo );
		$deps = $this->get_dependencias_ci();
		foreach( $deps as $dep ){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ] ejecutar '$dep'");
			$this->dependencias[$dep]->disparar_obtencion_datos_cn( $modo );
		}
	}

	function evt__obtener_datos_cn( $modo=null )
	{
		//Esta funcion hay que redefinirla en un hijo para OBTENER datos
		$this->log->warning($this->get_txt() . "[ evt__obtener_datos_cn ] No fue redefinido!");
	}

	//--  SALIDA  ----

	function disparar_entrega_datos_cn()
	{
		$this->log->debug( $this->get_txt() . "[ disparar_entrega_datos_cn ]");
		//DUDA: Validar aca es redundante?
		$this->evt__validar_datos();
		$this->evt__entregar_datos_cn();
		$deps = $this->get_dependencias_ci();
		foreach( $deps as $dep ){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->log->debug( $this->get_txt() . "[ disparar_entrega_datos_cn ] ejecutar '$dep'");
			$this->dependencias[$dep]->disparar_entrega_datos_cn();
		}
	}

	function evt__entregar_datos_cn()
	{
		//Esta funcion hay que redefinirla en un hijo para ENTREGAR datos
		$this->log->warning($this->get_txt() . "[ evt__entregar_datos_cn ] No fue redefinido!");
	}
	
	//-----------------------------------------------------------
	//-----------------------------------------------------------
	//---------  Eventos BASICOS
	//-----------------------------------------------------------
	//-----------------------------------------------------------
	
	function evt__cancelar()
	{
		$this->log->debug($this->get_txt() . "[ evt__cancelar ]");
		$this->cn->cancelar();
		$this->disparar_limpieza_memoria();
	}

	function evt__procesar()
	{
		$this->log->debug($this->get_txt() . "[ evt__procesar ]");
		$this->disparar_entrega_datos_cn();
		$this->cn->procesar();
		$this->disparar_limpieza_memoria();
	}	
	//-----------------------------------------------------------
}
?>