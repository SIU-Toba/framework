<?php 
class arbol_perfiles_funcionales extends toba_ei_arbol
{
	protected $modo_editable = false;
	
	function set_modo_editable() 
	{
		$this->modo_editable = true;
	}
	
	protected function generar_cuerpo()
	{
		$escapador = toba::escaper();
		$id_div = '';
		$id = "id='". $escapador->escapeHtmlAttr($this->objeto_js.'_nodo_raiz')."'";								
		if (count($this->_nodos_inicial) > 1) {
			$id_div = $id;
			$id = '';		
		}			
		if ($this->modo_editable) {
			echo toba_js::incluir('jquery.easyui.min.js');
		}
		
		echo "<div class='ei-cuerpo ei-arbol-cuerpo' $id_div>\n";
		foreach ($this->_nodos_inicial as $nodo_inicial) {
			echo "\n<ul $id class='ei-arbol-raiz'>";
			echo $this->recorrer_recursivo($nodo_inicial, true);
			echo '</ul>';
			$id = '';	//El id lo tiene solo el primer nodo
		}
		echo '</div>';	
	}	
	
	function mostrar_nodo(toba_nodo_arbol $nodo, $es_visible)
	{
		$salida = $this->mostrar_cambio_expansion($nodo, $es_visible);
		$salida .= $this->mostrar_iconos($nodo);

		//Nombre y ayuda
		$corto = $this->acortar_nombre($nodo->get_nombre_corto());
		$id = $nodo->get_id();
		$salida .= $corto;
		return $salida;
	}
	
	
	function recorrer_recursivo($nodo, $es_raiz=false, $nivel=0, $solo_contenido=false)
	{
		//Le paso al nodo una referencia al js del arbol que lo contiene
		if (method_exists($nodo, 'set_js_ei_arbol')) {			
			$nodo->set_js_ei_arbol($this->get_id_objeto_js());
		}
		$id_nodo = $nodo->get_id();
		$this->_ids_enviados[] = $id_nodo;
		//Verifico que no haya un mismo id perteneciente a clases diferentes
		if ($this->chequear_ids_unicos) {			
			if (isset($this->ids[$id_nodo])) {
				$clase = get_class($nodo);
				$clase_vieja = $this->ids[$id_nodo];
				throw new toba_error("Error al procesar el nodo '$id_nodo' de clase '$clase'. Ya existe el mismo id de clase '$clase_vieja'");
			}
			$this->ids[$id_nodo] = get_class($nodo);
		}
		
		//Genero el html para el nodo
		$salida_generada = $this->generar_fila_nodo($nodo, $nivel);
		
		//Diferencio la salida segun corresponda		
		if (! $solo_contenido) { 
			$estilo_li = '';							//Configuracion del estilo del nodo
			$clase_li = (! $es_raiz) ? 'ei-arbol-nodo menu-origen': 'ei-arbol-nodo';			
			if (method_exists($nodo, 'get_estilo_css_li')) {
				$estilo_li .= $nodo->get_estilo_css_li();
			}
			if (method_exists($nodo, 'get_clase_css_li')) {
				$clase_li .= $nodo->get_clase_css_li();
			}
			$escapador = toba::escaper();
			$salida = "\n\t<li class='". $escapador->escapeHtmlAttr($clase_li)."' id_nodo='". $escapador->escapeHtmlAttr($nodo->get_id())."' style='". $escapador->escapeHtmlAttr($estilo_li)."' >";					
			$salida .= $salida_generada;
			$salida .= "</li>\n";
		} else {
			$salida = $salida_generada;
		}
		
		return $salida;
	}
	
	/**
	 * @ignore
	 * @param mixed $nodo
	 * @param smallint $nivel
	 * @return string $salida
	 */
	function generar_fila_nodo($nodo, $nivel)
	{
		//Determina si el nodo es visible en la apertura
		$es_visible = $this->nodo_es_visible($nodo, $nivel);
		$salida = $this->mostrar_nodo($nodo, $es_visible);

		//Recursividad
		if (! $nodo->es_hoja()) {	
			$escapador = toba::escaper();
			//Configuracion del estilo del nodo
			$clase_ul = 'ei-arbol-rama menu-origen';			
			if (method_exists($nodo, 'get_clase_css_ul')) {
				$clase_ul .= $nodo->get_clase_css_ul();
			}
			
			$estilo_ul = ($es_visible) ? '' : 'display:none ';
			if (method_exists($nodo, 'get_estilo_css_ul')) {
				$estilo_ul .= $nodo->get_estilo_css_ul();
			}
			$estilo = ($estilo_ul != '') ? "style='". $escapador->escapeHtmlAttr($estilo_ul)."'" : '';
			
			$salida .= "\n<ul id_nodo='". $escapador->escapeHtmlAttr($nodo->get_id())."' class='". $escapador->escapeHtmlAttr($clase_ul)."' $estilo carpeta='true'>";			
			if ($nodo->tiene_hijos_cargados()) {
				$nivel ++;
				$salida .= $this->recorrer_hijos($nodo, $nivel);
			}
			$salida .= '</ul>';
		}		
		return $salida;
	}
}
?>