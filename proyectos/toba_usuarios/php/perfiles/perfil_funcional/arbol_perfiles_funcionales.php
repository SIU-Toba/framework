<?php 
class arbol_perfiles_funcionales extends toba_ei_arbol
{
	protected $nodos_activos = array();
	protected $nodos_inactivos = array();
	
	protected function generar_campos_hidden()
	{
		parent::generar_campos_hidden();
		echo toba_form::hidden($this->_submit.'__nodos_deseleccionados', '');
		echo toba_form::hidden($this->_submit.'__nodos_seleccionados', '');
	}

	private function cargar_estado_post()
	{
		$id_activos = $this->_submit.'__nodos_seleccionados';
		if (isset($_POST[$id_activos]) && $_POST[$id_activos] != '') {
			$this->nodos_activos = explode(apex_qs_sep_interno, $_POST[$id_activos]);
		}
		
		$id_inactivos = $this->_submit.'__nodos_deseleccionados';
		if (isset($_POST[$id_inactivos]) && $_POST[$id_inactivos] != '') {
			$this->nodos_inactivos = explode(apex_qs_sep_interno, $_POST[$id_inactivos]);
		}
	}
	
	function disparar_eventos()
	{
		$this->cargar_estado_post();
		
		//Aca valido los ids contra los enviados, para que nadie intente pasarse de vivo.
		foreach ($this->nodos_activos as $id_nodo) {
			$this->validar_id_nodo_recibido($id_nodo);
		}		
		foreach ($this->nodos_inactivos as $id_nodo) {
			$this->validar_id_nodo_recibido($id_nodo);
		}
	
		//Transmito el estado recuperado del post a cada nodo.
		if (isset($this->_nodos_inicial) && ! empty($this->_nodos_inicial)) {
			$raiz = $this->_nodos_inicial[0];
			$raiz->propagar_estado_hijos($this->nodos_activos, $this->nodos_inactivos);
		}
		
		parent::disparar_eventos();
	}
	
	function generar_fila_nodo($nodo, $nivel)
	{
		$nodo->desactivar_envio_inputs();
		return parent::generar_fila_nodo($nodo, $nivel);
	}
	
	function extender_objeto_js()
	{
		parent::extender_objeto_js();
		$id_js = $this->objeto_js;
		$id_gral = $this->_submit;
		$img_acceso = toba_recurso::imagen_toba('aplicar.png', false);
		$img_sin_acceso = toba_recurso::imagen_toba('prohibido.png', false);

		//Busco el estado de cada uno de los hijos involucrados en el pedido de pagina
		$estado = array('activos' => array(), 'inactivos' => array());				
		if (isset($this->_nodos_inicial) &&  ! empty($this->_nodos_inicial)) {
			$raiz = $this->_nodos_inicial[0];
			if ($raiz->tiene_hijos_cargados()) {
				foreach ($raiz->get_hijos() as $nodo) {
					$aux = $nodo->recuperar_estado_recursivo();
					$estado['activos'] = array_merge($estado['activos'], $aux['activos']);
					$estado['inactivos'] = array_merge($estado['inactivos'], $aux['inactivos']);					
				}
			}				
		}
		
		//Genero un par de arreglos que van a servir como lista en js
		$ids_activos = (! empty($estado['activos'])) ? array_fill_keys($estado['activos'], true): array();
		$ids_desactivados = (! empty($estado['inactivos'])) ? array_fill_keys($estado['inactivos'], true): array();
		$escapador = toba::escaper();
		echo ' var ' . $escapador->escapeJs($id_js . '_items_activos') . ' = '  . toba_js::arreglo($ids_activos, true) . "; \n" ;
		echo ' var ' . $escapador->escapeJs($id_js . '_items_desactivados') . ' = '  . toba_js::arreglo($ids_desactivados, true) ."; \n" ;				
		
		echo $escapador->escapeJs($id_js) .".cambiar_acceso = function(id_input)
			{
				var id_elemento = '". $escapador->escapeJs($id_gral)."' + '_' + id_input; 					
				if (isset(". $escapador->escapeJs($this->objeto_js.'_items_activos')."[id_input])) {			//Esta visible, hay que ocultarlo					
					delete(". $escapador->escapeJs($this->objeto_js.'_items_activos')."[id_input]);" .
					$escapador->escapeJs($this->objeto_js.'_items_desactivados')."[id_input] = true;
					$$(id_elemento + '_acceso_img').src = '". $escapador->escapeJs($img_sin_acceso)."';						
				} else if (isset(". $escapador->escapeJs($this->objeto_js.'_items_desactivados')."[id_input])) {		//Esta oculto, hay que mostrarlo					
					delete(". $escapador->escapeJs($this->objeto_js.'_items_desactivados')."[id_input]);
					". $escapador->escapeJs($this->objeto_js.'_items_activos')."[id_input] = true;
					$$(id_elemento + '_acceso_img').src = '". $escapador->escapeJs($img_acceso)."';									
				}
			}\n".
			
			$escapador->escapeJs($id_js).".marcar = function(id_input, valor)
			{				
				var id_final = '". $escapador->escapeJs($id_gral)."' + '_' + id_input + '_carpeta';
				var padre = $$(id_final).parentNode.parentNode;		
				var nodo = this.buscar_primer_marca(padre, 'UL');
				if (nodo) {		
					for (var i=0; i < nodo.childNodes.length; i++) {
						var hijo = nodo.childNodes[i];
						if (hijo.tagName && (hijo.tagName == 'LI')) {
							if (! this.buscar_primer_marca(hijo, 'UL')) {
								this.cambiar_estado_acceso(hijo, valor);
							} else {
								this.marcar_recursivo(hijo, valor);
							}
						}
					}
				}
			}\n".
						
			$escapador->escapeJs($id_js).".marcar_recursivo = function(carpeta, valor) 
			{
				var marca_carpeta = this.buscar_primer_marca(carpeta, 'SPAN');
				if (marca_carpeta) {
					for (var i=0; i < marca_carpeta.childNodes.length; i++) {
						var hc = marca_carpeta.childNodes[i];
						if (hc.tagName && (hc.tagName == 'INPUT')) {
							$$(hc.id).value = valor;
							$$(hc.id).checked = (valor == 0) ? true : false;
						}
					}
				}
				var nodo = this.buscar_primer_marca(carpeta, 'UL');		
				for (var i=0; i < nodo.childNodes.length; i++) {
					var hijo = nodo.childNodes[i];
					if (hijo.tagName && (hijo.tagName == 'LI')) {
						if (!this.buscar_primer_marca(hijo, 'UL')) {
							this.cambiar_estado_acceso(hijo, valor);
						} else {
							this.marcar_recursivo(hijo, valor);
						}
					}
				}
			}\n".
			
			$escapador->escapeJs($id_js).".cambiar_estado_acceso = function(nodo, valor)
			{
				for (var i=0; i < nodo.childNodes.length; i++) {
					if (nodo.childNodes[i].tagName == 'SPAN') {
						var hijo = nodo.childNodes[i];
						for (var j=0; j < hijo.childNodes.length; j++) {
							if (hijo.childNodes[j].tagName == 'IMG') {	
								hijo.childNodes[j].onclick();
							}
						}
					}
				}
			}\n".
			
			$escapador->escapeJs($id_js).".buscar_primer_marca = function (nodo, marca) {
				for (var i=0; i < nodo.childNodes.length; i++) {
					if (nodo.childNodes[i].tagName == marca) {
						return nodo.childNodes[i];
					}
				}
				return false;
			}\n".
			
			$escapador->escapeJs($id_js).".submit = function()
			{				
				var padre_esta_en_proceso = this.controlador && !this.controlador.en_submit();
				if (padre_esta_en_proceso) {
					return this.controlador.submit();
				}
				if (this._evento) {
					//Si es la selección de una semana marco la semana
					if (this._evento.id == 'ver_propiedades') {
						document.getElementById(this._input_submit + '__seleccion').value = this._evento.parametros;
					}
					//Marco la ejecucion del evento para que la clase PHP lo reconozca
					document.getElementById(this._input_submit).value = this._evento.id;			
				}
				document.getElementById(this._input_submit + '__apertura_datos').value = this.datos_apertura();				

				//Agrego como lista los nodos seleccionados y deseleccionados
				var claves = [];
				for (var i in ". $escapador->escapeJs($id_js.'_items_activos').") {
					claves.push(i);
				}
				document.getElementById('". $escapador->escapeJs($id_gral.'__nodos_seleccionados')."').value = claves.join(toba_hilo_separador_interno);

				var claves = [];				
				for (var i in ". $escapador->escapeJs($id_js.'_items_desactivados').") {
					claves.push(i);
				}
				document.getElementById('". $escapador->escapeJs($id_gral.'__nodos_deseleccionados')."').value = claves.join(toba_hilo_separador_interno);				
			}
		";
	}
}

?>