<?php
require_once('toba_molde_metodo_php.php');
require_once('toba_molde_metodo_js.php');
require_once('toba_molde_separador_php.php');
require_once('toba_molde_separador_js.php');

class toba_molde_clase
{
	protected $nombre;
	protected $nombre_ancestro;
	protected $orden = 0;
	protected $elementos_php = array();
	protected $indices_php = array();
	protected $elementos_js = array();
	protected $indices_js = array();
	protected $codigo_php = '';

	function __construct($nombre, $nombre_ancestro)
	{
		$this->nombre = $nombre;
		$this->nombre_ancestro = $nombre_ancestro;
	}

	//-- Contruccion del molde ------------------------------------

	function agregar(elemento_molde $elementos)
	{
		if ($elemento instanceof toba_molde_metodo_js || $elemento instanceof toba_molde_separador_js ) {
			$this->elementos_js[$this->orden] = $elemento;
			if ($elemento instanceof toba_molde_metodo_js ) {
				$this->indices_js[$elemento->get_nombre()] = $this->elementos_js[$this->orden];
			}
		} else {
			$this->elementos_php[$this->orden] = $elemento;
			if ($elemento instanceof toba_molde_metodo_php ) {
				$this->indices_php[$elemento->get_nombre()] = $this->elementos_php[$this->orden];
			}
		}
		$this->orden++;
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

	function get_lista_metodos()
	{
		$plan = array();
		$plan = generar_lista_elementos($this->elementos_php, 'PHP');
		$plan = array_merge($plan, generar_lista_elementos($this->elementos_js, 'JAVASCRIPT'));
		return $plan;
	}

	function generar_lista_elementos($elementos, $prefijo)
	{
		$lista = array();
		$titulo = '';
		$subtitulo = '';
		$a = 0;
		foreach ($elementos as $id => $elemento) {
			if(	$elemento instanceof toba_molde_separador ) {
				if( $elemento->get_tipo() == 'chico' ) {
					$subtitulo = $elemento->get_descripcion();
				} else {
					$titulo = $elemento->get_descripcion();
					$subtitulo = '';
				}
			} elseif( $elemento instanceof toba_molde_metodo ) {
				$desc = $prefijo . ' # ';
				$desc = isset($titulo) ? $desc . ' - ' . $titulo  : $desc;
				$desc = isset($subtitulo) ? $desc . ' - ' . $subtitulo  : $desc;
				$desc = $desc . ' > ' . $elemento->get_descripcion();
				$lista[$a]['id'] = $id;
				$lista[$a]['desc'] = $desc;
			}
			$a++;
		}
		return $lista;
	}

	//--------------------------------------------------------------
	//-- Generacion de codigo --------------------------------------
	//--------------------------------------------------------------

	function get_codigo($elementos_a_utilizar=null)
	{
		// Filtro el plan de generacion
		if (isset($elementos_a_utilizar)) {
			if(!is_array($elementos_a_utilizar)) {
				throw new error_toba('molde clase: La lista de elementos a incluir debe ser un array.');
			}			
			$this->filtrar_contenido($elementos_a_utilizar);
		} 
		// Genero el codigo
		return $this->generar_codigo();
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
			if (!in_array($id, $elementos_a_utilizar)) {
				unset($elementos[$id]);
			}
		}
	}

	/*
		Elimina los separadores de metodos que no se van a utilizar
		Los separadores chicos se eliminan si no tienen un metodo antes de otro separador o el final
		Los separadores grandes se eliminan si no tienen un metodo antes de otro separador grande o el final
	*/
	function colapasar_separadores( &$elementos )
	{
		$sep_chico_en_analisis = null;
		$sep_grande_en_analisis = null;
		foreach ($elementos as $id => $elemento) {
			if(	$elemento instanceof toba_molde_separador ) {
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
			} elseif( $elemento instanceof toba_molde_metodo ) {
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

	//-- Generacion de CODIGO ------------------------------------

	function generar_codigo()
	{
		$this->codigo_php .= "class {$this->nombre} extends {$this->padre_nombre}". salto_linea() ."{". salto_linea();
		$this->generar_codigo_php();
		$this->generar_codigo_js();
		$this->codigo_php .= "}". salto_linea();
		return $this->codigo_php;
	}		

	function generar_codigo_php()
	{
		foreach ($this->elementos_php as $elemento) {
			$elemento->identar(1);
			$this->codigo_php .= $elemento->get_codigo();
			$this->codigo_php .= salto_linea();
		}	
	}

	function generar_codigo_js()
	{
		$javascript = '';
		foreach ($this->elementos_js as $elemento) {
			$javascript .= $elemento->get_codigo();
			$javascript .= salto_linea();
		}
		if ($javascript) {
			$php = 'echo "' . salto_linea();
			$php .= $javascript;
			$php .= '";' . salto_linea();
			$metodo = new toba_molde_metodo_php('extender_objeto_js');
			$metodo->set_contenido($php);
			$this->codigo_php .= $metodo->get_codigo();
		}
	}
}
?>