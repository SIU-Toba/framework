<?php

class menu
{
	protected $consola;
	
	function __construct( $consola )
	{
		$this->consola = $consola;	
	}
	
	function get_titulo()
	{
		return "";	
	}
	
	function mostrar_observaciones()
	{
	}
	
	function get_comandos()
	{
		return array();	
	}

	//----------------------------------------------
	// Interface grafica
	//----------------------------------------------

	function get_info_comandos()
	{
		$info = array();
		$comandos = $this->get_comandos();
		foreach( $comandos as $comando )
		{
			$clase_comando = 'comando_' . $comando;
			require_once( $this->consola->get_ubicacion_comandos() .'/'.$clase_comando.'.php');
			$info[$clase_comando] = call_user_func( array( $clase_comando, 'get_info') );
		}
		return $info;
	}

	function mostrar_ayuda_raiz()
	{
		$this->consola->titulo( $this->get_titulo() );
		$this->mostrar_observaciones();
		$this->consola->enter();
		// Armo la coleccion de comandos
		$comandos = array();
		foreach ( $this->get_info_comandos() as $comando => $info ) {
			$comandos[substr($comando, strlen('comando_'))] = $info;
		}
		// Muestro la lista
		$this->consola->subtitulo("Comandos disponibles");
		$this->consola->coleccion( $comandos );
	}

	function mostrar_resumen()
	{
		$this->consola->enter();
		$this->consola->linea_completa( null, '_');
		$c = toba_cronometro::instancia();
		$tiempo = number_format($c->tiempo_acumulado(),3,",",".");
		$this->consola->mensaje("TIEMPO: $tiempo segundos");
		//print_r( $c->get_marcas() );
	}
}
?>