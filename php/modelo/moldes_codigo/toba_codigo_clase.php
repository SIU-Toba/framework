<?php
/**
 * @ignore
 */
class toba_codigo_clase
{
	protected $nombre;
	protected $nombre_ancestro;
	protected $orden = 0;
	protected $elementos_php = array();
	protected $indices_php = array();
	protected $elementos_js = array();
	protected $indices_js = array();
	protected $codigo_php = '';
	protected $analisis_muestra;
	protected $analisis_ventanas_utilizadas = array();
	protected $analisis_ventanas_no_utilizadas = array();
	protected $analisis_metodos_usuario = array();
	protected $ultimo_elemento;
	protected $propiedades = array();
	protected $archivos_requeridos = array();

	function __construct($nombre, $nombre_ancestro=null)
	{
		$this->nombre = $nombre;
		$this->nombre_ancestro = $nombre_ancestro;
	}

	//-- Contruccion del molde ------------------------------------

	function agregar(toba_codigo_elemento $elemento)
	{
		if ($elemento instanceof toba_codigo_metodo_js || $elemento instanceof toba_codigo_separador_js ) {
			$this->elementos_js[$this->orden] = $elemento;
			$this->ultimo_elemento = $this->elementos_js[$this->orden];
			if ($elemento instanceof toba_codigo_metodo_js) {
				$this->indices_js[$elemento->get_nombre()] = $this->elementos_js[$this->orden];
			}
		} elseif ($elemento instanceof toba_codigo_metodo_php || $elemento instanceof toba_codigo_separador_php ) {
			$this->elementos_php[$this->orden] = $elemento;
			$this->ultimo_elemento = $this->elementos_php[$this->orden];
			if ($elemento instanceof toba_codigo_metodo_php) {
				$this->indices_php[$elemento->get_nombre()] = $this->elementos_php[$this->orden];
			}
		} elseif($elemento instanceof toba_codigo_propiedad_php) {
			$this->propiedades[] = $elemento;	
		}else {
			throw new toba_error_asistentes('molde_clase: El elemento no corresponde a un tipo valido. Nombre: ' . $elemento->get_nombre() );	
		}
		$this->orden++;
	}
	

	function ultimo_elemento()
	{
		return $this->ultimo_elemento;
	}

	function agregar_bloque($elementos)
	{
		foreach($elementos as $elemento) {
			$this->agregar($elemento);
		}	
	}

	/*
		Devuelve una referencia a un metodo PHP
	*/
	function metodo_php($nombre)
	{
		if (isset($this->indices_php[$nombre])) {
			return $this->indices_php[$nombre];
		} else {
			throw new error_toba("molde clase: el metodo PHP '$nombre' no existe");	
		}
	}

	/*
		Devuelve una referencia a un metodo JS
	*/
	function metodo_js($nombre)
	{
		if (isset($this->indices_js[$nombre])) {
			return $this->indices_js[$nombre];
		} else {
			throw new error_toba("molde clase: el metodo JS '$nombre' no existe");	
		}
	}

	//-- Preguntas sobre la composicion del molde ------------------

	function get_lista_metodos($codigo_existente=null)
	{
		$plan = array();
		$plan = $this->generar_lista_elementos($this->elementos_php, 'PHP', $codigo_existente);
		$plan = array_merge($plan, $this->generar_lista_elementos($this->elementos_js, 'JAVASCRIPT', $codigo_existente));
		return $plan;
	}
	
	/**
		Genera una lista de los elementos que conforman el molde
	*/
	function generar_lista_elementos($elementos, $prefijo, $codigo_existente=null)
	{
		$lista = array();
		$titulo = '';
		$subtitulo = '';
		$a = 0;
		foreach ($elementos as $id => $elemento) {
			if(	$elemento instanceof toba_codigo_separador ) {
				//Filtra el separador según el código actual
				if (toba_archivo_php::codigo_tiene_codigo($codigo_existente, $elemento->get_codigo())) {
					continue;
				}					
				if( $elemento->get_tipo() == 'chico' ) {
					$subtitulo = $elemento->get_descripcion();
				} else {
					$titulo = $elemento->get_descripcion();
					$subtitulo = '';
				}
			} elseif( $elemento instanceof toba_codigo_metodo ) {

				//Filtra el metodo según el código actual				
				if ($elemento instanceof toba_codigo_metodo_js) {
					if (toba_archivo_php::codigo_tiene_metodo_js($codigo_existente, $elemento->get_descripcion())) {
						continue;
					}					
				} else {
					if (toba_archivo_php::codigo_tiene_metodo($codigo_existente, $elemento->get_descripcion())) {
						continue;
					}
				}				
				$desc = $prefijo . ' # ';
				$desc .= ($titulo && $subtitulo) ? $titulo.' - '.$subtitulo : $titulo.$subtitulo;
				$desc .=  ' => ' . $elemento->get_descripcion();
				$lista[$a]['id'] = $id;
				$lista[$a]['desc'] = $desc;
				$lista[$a]['elemento'] = $elemento;
			}
			$a++;
		}
		return $lista;
	}

	function agregar_archivo_requerido($require)
	{
		$this->archivos_requeridos[] = $require;	
	}

	function existe_elemento($id)
	{
		return isset($this->indices_php[$id]);	
	}	

	function get_indice_metodos_php()
	{
		$indice = array();
		foreach( $this->elementos_php as $elemento ) {
			$indice[] = $elemento->get_nombre();
		}
		return $indice;
	}
	
	//--------------------------------------------------------------
	//-- Generacion de codigo --------------------------------------
	//--------------------------------------------------------------

	function get_codigo($codigo_existente=null, $elementos_a_utilizar=null, $incluir_comentarios=true, $incluir_separadores=true)
	{
		// Filtro el plan de generacion
		if (isset($elementos_a_utilizar)) {
			if(!is_array($elementos_a_utilizar)) {
				throw new error_toba('molde clase: La lista de elementos a incluir debe ser un array.');
			}			
			$this->filtrar_contenido($elementos_a_utilizar);
		} 
		// Comentarios
		if( ! $incluir_comentarios ) {
			$this->filtrar_comentarios();	
		}
		// Separadores
		if( ! $incluir_separadores ) {
			$this->filtrar_separadores();	
		}		
		// Genero el codigo
		return $this->generar_codigo($codigo_existente);
	}

	//-- Filtro de contenido ------------------------------------

	function filtrar_contenido($elementos_a_utilizar)
	{
		$this->filtrar_metodos($this->elementos_php, $elementos_a_utilizar);
		$this->filtrar_metodos($this->elementos_js, $elementos_a_utilizar);
		$this->colapsar_separadores($this->elementos_php);
		$this->colapsar_separadores($this->elementos_js);
	}

	/*
		Borra los elementos JS y PHP que no estan en la lista de elementos a utilizar
		La lista de elementos a utilizar esta relacionada con la salida de get_plan_generacion
	*/
	function filtrar_metodos( &$elementos, $elementos_a_utilizar)
	{
		foreach( array_keys($elementos) as $id) {
			if ( ($elementos[$id] instanceof toba_codigo_metodo ) 
					&& (!in_array($id, $elementos_a_utilizar))) {
				unset($elementos[$id]);
			}
		}
	}

	/*
		Elimina los separadores de metodos que no se van a utilizar
		Los separadores chicos se eliminan si no tienen un metodo antes de otro separador o el final
		Los separadores grandes se eliminan si no tienen un metodo antes de otro separador grande o el final
	*/
	function colapsar_separadores( &$elementos )
	{
		$sep_chico_en_analisis = null;
		$sep_grande_en_analisis = null;
		foreach ($elementos as $id => $elemento) {
			if(	$elemento instanceof toba_codigo_separador ) {
				if( $elemento->get_tipo() == 'chico' ) {
					if( isset($sep_chico_en_analisis) ) {
						unset($elementos[$sep_chico_en_analisis]);
					}
					$sep_chico_en_analisis = $id;
				} else { //---GRANDE
					if( isset($sep_chico_en_analisis) ) {
						unset($elementos[$sep_chico_en_analisis]);
					}
					if( isset($sep_grande_en_analisis) ) {
						unset($elementos[$sep_grande_en_analisis]);
					}
					$sep_grande_en_analisis = $id;
				}
			} elseif( $elemento instanceof toba_codigo_metodo ) {
				$sep_chico_en_analisis = null;
				$sep_grande_en_analisis = null;
			}
		}	
		//Elimino los que no tienen un metodo antes del final
		if( isset($sep_chico_en_analisis) ) {
			unset($elementos[$sep_chico_en_analisis]);
		}
		if( isset($sep_grande_en_analisis) ) {
			unset($elementos[$sep_grande_en_analisis]);
		}
	}

	function filtrar_comentarios()
	{
		// PHP
		foreach( array_keys($this->elementos_php) as $id) {
			if ( $this->elementos_php[$id] instanceof toba_codigo_metodo_php ) {
				$this->elementos_php[$id]->set_mostrar_comentarios(false);
			}
		}		
		// Javascript
		foreach( array_keys($this->elementos_js) as $id) {
			if ( $this->elementos_js[$id] instanceof toba_codigo_metodo_js  )  {
				$this->elementos_js[$id]->set_mostrar_comentarios(false);
			}
		}			
	}
	
	function filtrar_separadores()
	{
		// PHP
		foreach( array_keys($this->elementos_php) as $id) {
			if ( $this->elementos_php[$id] instanceof toba_codigo_separador )  {
				unset($this->elementos_php[$id]);
			}
		}		
		// Javascript
		foreach( array_keys($this->elementos_js) as $id) {
			if ( $this->elementos_js[$id] instanceof toba_codigo_separador )  {
				unset($this->elementos_js[$id]);
			}
		}			
	}

	//-- Generacion de CODIGO ------------------------------------

	function generar_codigo($codigo_existente)
	{
		$this->codigo_php = $codigo_existente;
		
		//TODO: Falta implementar el agregado de requires a codigo existente
		if(count($this->archivos_requeridos)>0) {
			foreach($this->archivos_requeridos as $archivo) {
				$this->codigo_php .= "require_once('$archivo');" . "\n";
			}
			$this->codigo_php .= "\n";
		}
		//--Crea o reemplza la definicion de la clase
		if (! toba_archivo_php::codigo_tiene_clase($codigo_existente, $this->nombre)) {
			$extends = ($this->nombre_ancestro) ? "extends {$this->nombre_ancestro}" : "";
			$this->codigo_php .= "class {$this->nombre} $extends". "\n" ."{". "\n";
			$this->codigo_php .= "}". "\n";
			$this->generar_codigo_php($this->codigo_php);
			$this->generar_codigo_js($this->codigo_php);
		} else {
			$this->generar_codigo_php($codigo_existente);
			$this->generar_codigo_js($codigo_existente);
		}
		return $this->codigo_php;
	}		

	/**
	 * @todo: Falta implementar el reemplazo de propiedades
	 */	
	function generar_propiedades($codigo_existente)
	{
			//TODO: Asume que el codigo_existente es algo que aun no tiene propiedades
			$posicion_arranque = strpos( $codigo_existente,'{');
			$codigo_propiedades = '';
			foreach($this->propiedades as $propiedad) {
				$propiedad->identar(1);
				$codigo_propiedades .= $propiedad->get_codigo();
			}
			$codigo_propiedades .= "\n";
			if ($posicion_arranque !== false){
				$codigo_propiedades = '{' . "\n". $codigo_propiedades;
				$una_vez = 1;											//Maldito PHP sos el colmo!
				$this->codigo_php = str_replace('{', $codigo_propiedades, $this->codigo_php, $una_vez);
			}else{
				$this->codigo_php .= $codigo_propiedades;
			}
	}

	function generar_codigo_php($codigo_existente='')
	{
		if(count($this->propiedades)>0) {
			$this->generar_propiedades($codigo_existente);
		}
		foreach ($this->elementos_php as $elemento) {
			$elemento->identar(1);
			if ($elemento instanceof toba_codigo_metodo_php &&
		 			toba_archivo_php::codigo_tiene_metodo($codigo_existente, $elemento->get_nombre()) )	{
				//Reemplaza el metodo
				$this->codigo_php = toba_archivo_php::reemplazar_metodo($this->codigo_php, 
															$elemento->get_nombre(), 
															$elemento->get_codigo());

			} else {
				//Evita por ejemplo que se agreguen secciones repetidas
				if (! toba_archivo_php::codigo_tiene_codigo($codigo_existente, $elemento->get_codigo())) {
					//Agrego el metodo en un lugar adecuado
					$this->codigo_php = toba_archivo_php::codigo_agregar_metodo($this->codigo_php, $elemento->get_codigo());
		 		}					
		 	}
		}	
	}

	/**
	 * @todo: Falta implementar el reemplazo
	 */
	function generar_codigo_js($codigo_existente='')
	{
		$nombre_metodo_php = 'extender_objeto_js';
		$javascript = '';
		foreach ($this->elementos_js as $elemento) {
			if($javascript) $javascript .= "\n";
			$javascript .= $elemento->get_codigo();
		}
		if ($javascript) {
			if (!toba_archivo_php::codigo_tiene_metodo($codigo_existente, $nombre_metodo_php) )	{
				//--Crea el metodo php
				$separador = new toba_codigo_separador_php('JAVASCRIPT',null,'grande');				
				$separador->identar(1);
				
				$php = 'echo "' . "\n";
				$php .= $javascript;
				$php .= '";';
				$metodo = new toba_codigo_metodo_php('extender_objeto_js');
				$metodo->set_contenido($php);
				$metodo->identar(1);
				$codigo = $separador->get_codigo()."\n".$metodo->get_codigo();
				$this->codigo_php = toba_archivo_php::codigo_agregar_metodo($this->codigo_php, $codigo);
			} else {
				//--Agrega al metodo existente
				$metodo = new toba_codigo_metodo_php('extender_objeto_js');
				$codigo_actual = toba_archivo_php::codigo_get_metodo($this->codigo_php, $nombre_metodo_php);
				//Busca el cerrado del string de javascript
				$pos = strrpos($codigo_actual, '";');
				if ($pos !== false) {
					$php = toba_archivo_php::codigo_quitar_identacion(substr($codigo_actual,0, $pos), 2);
					$php .= $javascript;
					$php .= toba_archivo_php::codigo_quitar_identacion(substr($codigo_actual, $pos), 2);
					$metodo->set_contenido($php);
					$metodo->identar(1);
					$this->codigo_php = toba_archivo_php::reemplazar_metodo($this->codigo_php, $nombre_metodo_php, $metodo->get_codigo());
				}
			}

		}
	}

	//--------------------------------------------------------------
	//-- Analisis de codigo ----------------------------------------
	//--------------------------------------------------------------

	function set_muestra_analisis(ReflectionClass $muestra)
	{
		$this->analisis_muestra = $muestra;
		$this->analizar_php();
		$this->analizar_js();
	}
	
	function analizar_php()
	{
		foreach ($this->analisis_muestra->getMethods() as $metodo) {
			$n = $metodo->getName();
			if ( $n == 'extender_objeto_js' ) continue;
			if ($metodo->getDeclaringClass() == $this->analisis_muestra) {
				if (isset($this->indices_php[$n])) {
					$this->analisis_ventanas_utilizadas[] = $n;
				} else {
						$this->analisis_metodos_usuario[] = $n;
				}
			}
		}
		$metodos_molde = array_keys($this->indices_php);
		$this->analisis_ventanas_no_utilizadas = array_diff($metodos_molde, $this->analisis_ventanas_utilizadas);
	}

	function analizar_js()
	{
	}

	function get_ventanas_utilizadas()
	{
		return $this->analisis_ventanas_utilizadas;
	}

	function get_ventanas_no_utilizadas()
	{
		return $this->analisis_ventanas_no_utilizadas;		
	}

	function get_metodos_usuario()
	{
		return $this->analisis_metodos_usuario;
	}
	
	function get_analisis()
	{
		$datos[$this->nombre]['Ventanas Utilizadas'] = 	$this->analisis_ventanas_utilizadas;	
		$datos[$this->nombre]['Ventanas No Utilizadas'] = $this->analisis_ventanas_no_utilizadas;	
		$datos[$this->nombre]['Metodos Usuario'] = $this->analisis_metodos_usuario;	
		return $datos;
	}
	//--------------------------------------------------------------

	/**
	 * Guarda el código que genera esta clase en $path
	 * @param string $path
	 */
	function guardar($path)
	{
		$codigo = $this->get_codigo();
		// Agregamos los tags php
		$codigo = "<?php\n$codigo?>";
		file_put_contents($path, $codigo);
	}
}
?>