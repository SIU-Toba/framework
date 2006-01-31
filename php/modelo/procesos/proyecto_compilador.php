<?
require_once('modelo/lib/proceso.php');
require_once('nucleo/componentes/catalogo_toba.php');
require_once('nucleo/componentes/cargador_toba.php');
require_once('nucleo/lib/reflexion/clase_datos.php');
require_once('nucleo/lib/manejador_archivos.php');

class proyecto_compilador extends proceso
{
	const archivo_referencia = 'tabla_tipos';
	const prefijo_componentes = 'php_';	
	protected $tabla_tipos;
	
	function procesar()
	{
		$this->compilar_componentes();
		$this->crear_archivo_referencia();
	}
	
	/*
	*	Ciclo de compilacion de componentes
	*/
	function compilar_componentes()
	{
		foreach (catalogo_toba::get_lista_tipo_componentes() as $tipo) {
			$this->interface->titulo( $tipo );
			$path = $this->elemento->get_dir_componentes_compilados() . '/' . $tipo;
			manejador_archivos::crear_arbol_directorios( $path );
			foreach (catalogo_toba::get_lista_componentes( $tipo, $this->elemento->get_id() ) as $id_componente) {
				$this->compilar_componente( $tipo, $id_componente );
			}
		}
	}
	
	/*
	*	Compila un componente
	*/
	function compilar_componente( $tipo, $id )
	{
		//Armo la clase compilada
		$nombre = manejador_archivos::nombre_valido( self::prefijo_componentes . $id['componente'] );
		$this->interface->mensaje("Compilando: " . $id['componente']);
		$clase = new clase_datos( $nombre, basename(__FILE__) );		
		$metadatos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
		$clase->agregar_metodo_datos('get_metadatos',$metadatos);
		//Creo el archivo
		$directorio = $this->elemento->get_dir_componentes_compilados() . '/' . $tipo;
		$path = $directorio .'/'. $nombre . '.php';
		$clase->guardar( $path );
		//Creo la tabla de referencia
		/*	ATENCION! excluyo los items porque pueden pisarse los IDs con los objetos	*/
		if ( $tipo != 'item' ) {
			$this->tabla_tipos[$id['componente']] = $tipo;
		}
	}

	/*
	*	Creo la tabla de referencias
	*/
	function crear_archivo_referencia()
	{
		//Armo la clase compilada
		$this->interface->mensaje("Creando tabla de tipos.");
		$clase = new clase_datos( self::archivo_referencia, basename(__FILE__) );		
		$clase->agregar_metodo_datos('get_datos',$this->tabla_tipos);
		//Creo el archivo
		$archivo = manejador_archivos::nombre_valido( self::archivo_referencia );
		$path = $this->elemento->get_dir_componentes_compilados() .'/'. $archivo . '.php';
		$clase->guardar( $path );
	}
}
?>