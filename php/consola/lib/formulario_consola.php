<?

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
			throw new toba_excepcion("CONSOLA: Formulario mal definido");
		}
		$this->campos[ $parametros['id'] ] = $parametros;
	}
	
	function procesar()
	{
		$this->obtener_valores();
		do {
			$this->listar_valores();
			$this->interface->enter();
			$ok = $this->interface->dialogo_simple('Los valores ingresados son correctos?');
			if ( ! $ok ) {
				//$this->corregir_valores();			
				$this->obtener_valores();
			}
		} while ( ! $ok );
		// Devuelvo el resultado
		foreach( $this->campos as $campo ) {
			$datos[ $campo['id'] ] = $campo['valor'];	
		}
		return $datos;
	}
	
	function obtener_valores()
	{
		$this->interface->enter();
		$this->interface->subtitulo( $this->titulo );
		foreach( array_keys( $this->campos ) as $campo ) {
			$valor = $this->interface->dialogo_ingresar_texto( $this->campos[$campo]['nombre'] );
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
	
	// Deberia ser una forma mas copada de obtener_valores
	function corregir_valores()
	{
	}	
}
?>