<?php
require_once('archivo_php.php');
define('toba_clase_php_separador','_|_');
/**
*	Representa una CLASE del ambiente. 
*	Tiene capacidades de generacion y analisis si se le asocia la metaclase correspondiente
*	a la clase cargada
*/
class clase_php
{		
	protected $nombre;
	protected $archivo;
	protected $padre_nombre;
	protected $archivo_padre_nombre;
	protected $meta_clase;				//la clase que conoce el contenido de la clase que se esta editando
	//Opciones de generacion
	protected $opciones_generacion;	
	
	function __construct($nombre, $archivo, $clase_padre_nombre, $archivo_padre_nombre)
	{
		$this->nombre = $nombre;
		$this->archivo = $archivo;
		$this->padre_nombre = str_replace('objeto_', 'toba_', $clase_padre_nombre);
		$this->archivo_padre_nombre = $archivo_padre_nombre;
	}
	
	function nombre()
	{
		return $this->nombre;
	}
	
	//Asocia la METACLASE
	function set_meta_clase($meta_clase)
	{
		$this->meta_clase = $meta_clase;
	}

	function incluir_clase_padre()
	{
		require_once($this->archivo_padre_nombre);
	}	
	
	//---------------------------------------------------------------
	//-- Generacion de codigo
	//---------------------------------------------------------------

	/**
	*	Informa la lista de metodos a generar
	*/
	function get_lista_metodos_posibles() 
	{
		$a = 0;
		$lista_metodos = array();
		$plan = $this->meta_clase->get_plan_construccion_metodos();
		foreach( $plan as $id_seccion => $seccion ) {
			foreach( $seccion['bloque'] as $id_bloque => $bloque ) {
				foreach( $bloque['metodos'] as $id_metodo => $metodo) {
					$m = isset($seccion['desc']) ? $seccion['desc'] . ' - ' : '';
					$m .= isset($bloque['desc']) ? $bloque['desc'] . ' - ' : '';
					$m .= $id_metodo;
					$lista_metodos[$a]['id'] = $id_seccion . 
												toba_clase_php_separador.$id_bloque .
												toba_clase_php_separador.$id_metodo;
					$lista_metodos[$a]['desc'] = $m;
					$a++;
				}
			}
		}
		return $lista_metodos;
	}

	/**
	*	Genera la clase
	*/
	function generar($opciones)
	{
		//echo "<br><br><pre style='background-color: white'>" . $this->generar_clase($opciones) . "</pre>";
		//return;
		if ($this->archivo->esta_vacio()) {
			$this->archivo->crear_basico();
		}
		$this->archivo->edicion_inicio();
		$this->archivo->insertar_al_final($this->generar_clase($opciones));
		$this->archivo->edicion_fin();
	}
	
	function generar_clase($opciones)
	{
		//Incluir el código que hace la subclase
		$codigo = '';
		/*if ( ! $this->archivo->esta_vacio() ) {
			$codigo .= $this->separador_clases();
		}*/
		$codigo .= "class {$this->nombre} extends {$this->padre_nombre}\n{\n";
		$codigo .= $this->generar_cuerpo_clase($opciones) ."\n";		
		$codigo .= "}\n";
		return $codigo;
	}

	function generar_cuerpo_clase($opciones)
	{
		$clase = '';
		if(is_array($opciones)) {
			$this->registrar_opciones_generacion($opciones);
			$plan = $this->meta_clase->get_plan_construccion_metodos();
			foreach( $plan as $id_seccion => $seccion ) {
					//----------- JAVASCRIPT -----------------
				if($id_seccion == 'javascript') {
					$js[] = "echo \"";
					foreach( $seccion['bloque'] as $id_bloque => $bloque ) {
						$funcion_js = array();
						foreach( $bloque['metodos'] as $id_metodo => $metodo) {
							if ($this->metodo_incluido($id_seccion, $id_bloque, $id_metodo) ) {
								$funcion_js = array_merge($funcion_js, $this->generar_metodo_js($id_metodo, $metodo['parametros']));
								$funcion_js[] = '';
							}
						}
						if ( count($funcion_js) > 0 ) {
							if(isset($bloque['desc'])) {
								$js[] = $this->separador_seccion_js($bloque['desc']);	
								$js[] = '';
							}
							$js = array_merge($js, $funcion_js);
						}
					}
					$js[] = "\";";
					if( count($js) > 2 ) { //Se genero al menos uno?
						if(isset($seccion['desc'])) $clase .= $this->separador_seccion_grande($seccion['desc']);
						$clase .= $this->generar_metodo_php('extender_objeto_js', array(), $js);
					}
				} else {
					//----------- PHP -------------------------
					$temp_seccion = '';
					foreach( $seccion['bloque'] as $id_bloque => $bloque ) {
						$temp_bloque = '';
						foreach( $bloque['metodos'] as $id_metodo => $metodo) {
							if ($this->metodo_incluido($id_seccion, $id_bloque, $id_metodo) ) {
								$temp_bloque .= $this->generar_metodo_php($id_metodo, $metodo['parametros'], array(), $metodo['comentarios'] );
							}
						}
						if($temp_bloque != '') {
							if(isset($bloque['desc'])) $temp_bloque = $this->separador_seccion_chica($bloque['desc']) . $temp_bloque;
						}
						$temp_seccion .= $temp_bloque;
					}
					if($temp_seccion != '') {
						if(isset($seccion['desc'])) $clase .= $this->separador_seccion_grande($seccion['desc']);
						$clase .= $temp_seccion;
					}
				}
			}
		}
		return $clase;
	}
	
	function registrar_opciones_generacion($opciones)
	{
		$this->opciones_generacion = $opciones;	
	}
	
	function metodo_incluido($seccion, $bloque, $metodo) {
		$id = $seccion .toba_clase_php_separador. $bloque .toba_clase_php_separador. $metodo;
		return in_array($id, $this->opciones_generacion);
	}
	
	//---------------------------------------------------------------------------------
	//----  Utilerías de formateo para la generación  ---------------------------------
	//---------------------------------------------------------------------------------

	static function generar_metodo_php($nombre,$parametros=array(),$contenido=array(),$comentarios=array())
	{
		if(!is_array($comentarios)){
			throw new toba_error("Error en el metodo: $nombre. Los comentarios debern ser un array");	
		}
		if(!is_array($parametros)){
			throw new toba_error("Error en el metodo: $nombre. Los parametros debern ser un array");	
		}
		if(!is_array($contenido)){
			throw new toba_error("Error en el metodo: $nombre. El contenido debe ser un array");	
		}
		$identado = "\t";
		//Armo parametros
		$php_parametros = '';
		if(is_array($parametros)){
			for($a=0;$a<count($parametros);$a++){
				$parametros[$a] = '$' . $parametros[$a];
			}
			$php_parametros = implode(', ',$parametros);
		}
		//Armo la funcion
		$funcion = '';
		foreach($comentarios as $fila) {
			$funcion .= "$identado//$fila\n";
		}
		$funcion = "${identado}function $nombre($php_parametros)\n";
		$funcion .= "$identado{\n";
		foreach($contenido as $fila) {
			$funcion .= "$identado\t$fila\n";
		}
		$funcion .= "$identado}\n\n";
		return $funcion;
	}

	static function generar_metodo_js($nombre,$parametros=array(),$contenido=array(),$comentarios=array())
	{
		//Armo parametros
		$js_parametros = '';
		if(is_array($parametros)){
			$js_parametros = implode(', ',$parametros);
		}
		//Armo la funcion
		$funcion = array();
		foreach($comentarios as $fila) {
			$funcion[] = "$fila";
		}
		$funcion[] = "{\$this->objeto_js}.$nombre = function($js_parametros)";
		$funcion[] = "{";
		foreach($contenido as $fila) {
			$funcion[] = "\t$fila";
		}
		$funcion[] = "}";
		return $funcion;
	}

	static function separador_clases()
	{
		return "//--------------------------------------------------------------------\n";	
	}

	static function separador_seccion_chica($nombre='')
	{	
		return "\t//---- $nombre -------------------------------------------------------\n\n";	
	}	
	
	static function separador_seccion_grande($nombre)
	{
		return  "\t//-------------------------------------------------------------------\n".
				"\t//--- $nombre\n".
				"\t//-------------------------------------------------------------------\n\n";
	}
	
	static function separador_seccion_js($nombre)
	{
		return "//---- $nombre -----------------------------------------";
	}

	//---------------------------------------------------------------
	//-- Analisis de codigo
	//---------------------------------------------------------------
	
	function analizar()
	{
		$this->incluir_clase_padre();
		$this->archivo->incluir();		
		try {
			$clase = new ReflectionClass($this->nombre);
			$metodos = $clase->getMethods();
			
			echo "<div style='text-align: left;'><h3>Clase ".$clase->getName()."</h3>";
			echo "<ul>";
			//Métodos propios
			$this->analizar_metodos('propios', $clase, $metodos, true);
			$padre = $clase->getParentClass();
			while ($padre != null) {
				$titulo = "heredados de {$padre->getName()}";
				$this->analizar_metodos($titulo, $padre, $metodos, false);
				$padre = $padre->getParentClass();
			}
			echo "</ul></div>";
		} catch (Exception $e) {
			echo ei_mensaje("No se encuentra la clase {$this->nombre} en este archivo.", "error");
		}
	}	
	
	function analizar_metodos($titulo, $clase, $metodos, $mostrar=true)
	{
		static $id=0;$id++;
		$display = ($mostrar) ? "" : "style='display: none'";
		echo "<li><a href='#' onclick=\"o = getElementById('_id$id'); o.style.display = (o.style.display == 'none') ? '' : 'none';\">";
		echo "Métodos $titulo</a></li>\n";
		echo "<ul id='_id$id' $display>\n";
		foreach ($metodos as $metodo) {
			if ($metodo->getDeclaringClass() == $clase) {
				$estilo = '';
				if (isset($this->meta_clase)){
					if ($this->meta_clase->es_evento($metodo->getName())) {
						$tipo = toba_recurso::imagen_apl('reflexion/desconocido.gif');
						if (! $this->meta_clase->es_evento_valido($metodo->getName())) {
							$tipo = toba_recurso::imagen_apl('reflexion/problema.gif');
						}
						if ($this->meta_clase->es_evento_sospechoso($metodo->getName())) {
							$tipo = toba_recurso::imagen_apl('warning.gif');
						}
						if ($this->meta_clase->es_evento_predefinido($metodo->getName())) {
							$tipo = toba_recurso::imagen_apl('reflexion/evento.gif');
						}
						$estilo =  "list-style-image: url($tipo)";					
					} 
				}
				echo "<li style='padding-right: 10px; $estilo'>&nbsp;";
				echo $metodo->getName();
				echo "</li>\n";
			}
		}	
		echo "</ul></li>\n";	
	}
}		
?>