<?php

class formulario_consola
{
	private $interface;
	private $titulo;
	private $campos;
	private $valores;
	
	function __construct(  $manejador_interface, $titulo )
	{
		$this->interface = $manejador_interface;
		$this->titulo = $titulo;
	}
	
	function agregar_campo( $parametros )
	{
		if ( !isset( $parametros['id'] ) || !isset( $parametros['nombre'] ) ) {
			throw new toba_error("CONSOLA: Formulario mal definido");
		}
		$this->campos[ $parametros['id'] ] = $parametros;
	}
	
	function procesar()
	{
		$this->get_valores();
		do {
			$this->listar_valores();
			$this->interface->enter();
			$ok = $this->interface->dialogo_simple('Los valores ingresados son correctos?');
			if ( ! $ok ) {
				//$this->corregir_valores();			
				$this->get_valores();
			}
		} while ( ! $ok );
		// Devuelvo el resultado
		foreach( $this->campos as $campo ) {
			$datos[ $campo['id'] ] = $campo['valor'];	
		}
		return $datos;
	}
	
	function get_valores()
	{
		$this->interface->enter();
		$this->interface->subtitulo( $this->titulo );
		foreach( array_keys( $this->campos ) as $campo ) {
			$obligatorio = (! isset($this->campos[$campo]['obligatorio']) || $this->campos[$campo]['obligatorio']);
			$valor = $this->interface->dialogo_ingresar_texto( $this->campos[$campo]['nombre'], $obligatorio);
			$this->campos[$campo]['valor'] = $valor;
		}
	}

	function listar_valores()
	{
		$this->interface->enter();
		$this->interface->subtitulo( 'Valores recolectados' );
		foreach( $this->campos as $campo ) {
			unset( $campo['id'] );
			$datos[] = $campo;	
		}
		$this->interface->tabla( $datos, array( 'Campo', 'Valor'));
	}
	
	// Deberia ser una forma mas copada de get_valores
	function corregir_valores()
	{
	}	
}
?>