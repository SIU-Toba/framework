<?php 
class arbol_restricciones_funcionales extends toba_ei_arbol
{
	
	protected $nodos_activos = array();
	protected $nodos_inactivos = array();

	protected function generar_campos_hidden()
	{
		parent::generar_campos_hidden();
		echo toba_form::hidden($this->_submit.'__nodos_invisibles', '');
		echo toba_form::hidden($this->_submit.'__nodos_visibles', '');
	}

	private function cargar_estado_post()
	{
		$id_activos = $this->_submit.'__nodos_visibles';
		if (isset($_POST[$id_activos]) && $_POST[$id_activos] != '') {
			$this->nodos_activos = explode(apex_qs_sep_interno, $_POST[$id_activos]);
		}
		
		$id_inactivos = $this->_submit.'__nodos_invisibles';
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
	
	function servicio__ejecutar()
	{
		toba::memoria()->desactivar_reciclado();
		$id_nodo = toba::memoria()->get_parametro('id_nodo');
		$nodo = $this->reportar_evento_interno('cargar_nodo', $id_nodo);
		if (isset($nodo) && $nodo !== apex_ei_evt_sin_rpta) {
			$html = $this->recorrer_hijos(current($nodo), 0);
			$html .= '[--toba--]';	
			$html .= toba_js::abrir();
			$html .= $this->actualizar_estado_js(current($nodo));
			$html .= toba_js::cerrar();
			$html .= '[--toba--]';	
			echo $html;
		} else {
			toba::logger()->warning("toba_ei_arbol: No se pudo obtener el nodo que representa al ID $id_nodo");
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------//
	//						JAVASCRIPT
	//-------------------------------------------------------------------------------------------------------------//
	function recuperar_estado_nodos($nodo_raiz)
	{		
		$estado = array('activos' => array(), 'inactivos' => array());	
		if (isset($nodo_raiz)) {
			//Recopilo el estado de los nodos y los hijos
			if ($nodo_raiz->tiene_hijos_cargados()) {			
				foreach ($nodo_raiz->get_hijos() as $hijo) {
					$aux = $hijo->recuperar_estado_recursivo();
					$estado['activos'] = array_merge($estado['activos'], $aux['activos']);
					$estado['inactivos'] = array_merge($estado['inactivos'], $aux['inactivos']);
				}
			}		
		}
		return $estado;		
	}
	
	function actualizar_estado_js($nodo)
	{
		$js_code = '';		
		$estado = $this->recuperar_estado_nodos($nodo);		//Busco el estado de los nodos
		if (! empty($estado['activos'])) {
			$js_code .= $this->objeto_js .'.agregar_activos(' . toba_js::arreglo(array_fill_keys($estado['activos'], true), true) . ');';
		}
		if (! empty($estado['inactivos'])) {
			$js_code .= $this->objeto_js .'.agregar_inactivos(' . toba_js::arreglo(array_fill_keys($estado['inactivos'], true), true) . ');';
		}		
		return $js_code;
	}	
	
	function extender_objeto_js()
	{
		parent::extender_objeto_js();
		$img_oculto = toba_recurso::imagen_toba('no-visible.png', false);
		$img_visible = toba_recurso::imagen_toba('visible.png', false);
		$img_solo_lectura = toba_recurso::imagen_toba('no-editable.gif', false);
		$img_editable = toba_recurso::imagen_toba('editable.gif', false);
		
		echo "var {$this->objeto_js}_nodo_rf_activas = []; \n";
		echo "var {$this->objeto_js}_nodo_rf_inactivas = [];\n";	
		echo "			
			{$this->objeto_js}.agregar_activos = function(nuevos)
			{
				for (var key in nuevos) {
					{$this->objeto_js}_nodo_rf_activas[key] = nuevos[key];
				}				
			}
			
			{$this->objeto_js}.agregar_inactivos = function(nuevos)
			{
				for (var key in nuevos) {
					{$this->objeto_js}_nodo_rf_inactivas[key] = nuevos[key];
				}				
			}

			{$this->objeto_js}.cambiar_oculto = function(id_nodo)
			{
				var activos = {$this->objeto_js}_nodo_rf_activas;
				var inactivos = {$this->objeto_js}_nodo_rf_inactivas;
				var id_input = '{$this->_submit}_' + id_nodo + '_oculto_img';

				if (isset(activos[id_nodo])) {							//Esta visible, hay que ocultarlo
					delete( {$this->objeto_js}_nodo_rf_activas[id_nodo]);
					 {$this->objeto_js}_nodo_rf_inactivas[id_nodo] = true;								
					$$(id_input).src = '$img_oculto';						 
						
				} else if (isset(inactivos[id_nodo])) {						//Esta oculto, hay que mostrarlo
					delete( {$this->objeto_js}_nodo_rf_inactivas[id_nodo]);
					 {$this->objeto_js}_nodo_rf_activas[id_nodo] = true;							
					$$(id_input).src = '$img_visible';										 
				}
			}
			
			{$this->objeto_js}.cambiar_editable = function(id_input) 
			{
				var valor_actual = $$(id_input).value;
				if (valor_actual == 1) {					//Esta oculto, hay que mostrarlo					
					$$(id_input + '_img').src = '$img_editable';
					$$(id_input).value = 0;
				} else {								//Esta visible, hay que ocultarlo					
					$$(id_input + '_img').src = '$img_solo_lectura';
					$$(id_input).value = 1;
				}
			}
			

			{$this->objeto_js}.submit = function()
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
				for (var i in {$this->objeto_js}_nodo_rf_activas) {
					claves.push(i);
				}
				document.getElementById('{$this->_submit}__nodos_visibles').value = claves.join(toba_hilo_separador_interno);

				var claves = [];				
				for (var i in {$this->objeto_js}_nodo_rf_inactivas) {
					claves.push(i);
				}
				document.getElementById('{$this->_submit}__nodos_invisibles').value = claves.join(toba_hilo_separador_interno);
			}
			

			{$this->objeto_js}.retorno_expansion = function(resultado)
			{
				var partes = toba.analizar_respuesta_servicio(resultado);							//Tengo que separar el HTML del JS aca
				if (partes === false) {
					notificacion.agregar('Se produjo un error de comunicación, intente reiniciar la operación');
					return false;
				}
				
				var nodo = resultado.argument;				//Busco el lugar donde insertar la respuesta
				var ul = this.buscar_primer_ul(nodo.parentNode);		
				if (ul) {
					if (partes[1] != '') {ejecutar_scripts(partes[1]); }
					ul.innerHTML = partes[0];					
					this.toggle_expansion(nodo,ul);					
				}
				return true;
			};";
		if (! empty($this->_nodos_inicial)) {				
			echo $this->actualizar_estado_js($this->_nodos_inicial[0]);
		}
	}			
}
?>