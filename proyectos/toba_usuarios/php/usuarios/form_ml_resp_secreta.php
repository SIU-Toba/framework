<?php
class form_ml_resp_secreta extends toba_ei_formulario_ml
{
	
	protected function generar_formulario_cuerpo()
	{
		$escapador = toba::escaper();
		echo '<tbody>';			
		if ($this->_registro_nuevo !== false) {
			$template = (is_array($this->_registro_nuevo)) ? $this->_registro_nuevo : array();
			$this->agregar_registro($template);
		}
		//------ FILAS ------
		$this->_filas_enviadas = array();
		if (!isset($this->_ordenes)) {
			$this->_ordenes = array();
		}
		//Se recorre una fila más para insertar una nueva fila 'modelo' para agregar en js
		if ( $this->_info_formulario['filas_agregar'] && $this->_info_formulario['filas_agregar_online']) {
			$this->_datos['__fila__'] = array();
			$this->_ordenes[] = '__fila__';
		}
		$a = 0;		
		foreach ($this->_ordenes as $fila) {
			$dato = $this->_datos[$fila];
			//Si la fila es el template ocultarla
			if ($fila !== '__fila__') {
				$this->_filas_enviadas[] = $fila;
				$estilo_fila = '';
				$es_fila_modelo = false;
			} else {
				$estilo_fila = "style='display:none;'";
				$es_fila_modelo = true;
			}
			//Determinar el estilo de la fila
			if (isset($this->_clave_seleccionada) && $fila == $this->_clave_seleccionada) {
				$this->estilo_celda_actual = 'ei-ml-fila-selec';				
			} else {
				$this->estilo_celda_actual = 'ei-ml-fila';
			}
			$this->cargar_registro_a_ef($fila, $dato);
			//--- Se cargan las opciones de los efs de esta fila
			$this->_carga_opciones_ef->cargar();
			//--- Ventana para poder configurar una fila especifica
			$callback_configurar_fila_contenedor = 'conf_fila__' . $this->_parametros['id'];
			if (method_exists($this->controlador, $callback_configurar_fila_contenedor)) {
				$this->controlador->$callback_configurar_fila_contenedor($fila);
			}			
			//-- Inicio html de la fila
			echo "\n<!-- FILA ". $escapador->escapeHtml($fila)." -->\n\n";			
			echo "<tr $estilo_fila id='". $escapador->escapeHtmlAttr("{$this->objeto_js}_fila$fila")."' onclick='". $escapador->escapeHtmlAttr($this->objeto_js).".seleccionar(". $escapador->escapeHtmlAttr($fila).")'>";
			if ($this->_info_formulario['filas_numerar']) {
				echo "<td class='". $escapador->escapeHtmlAttr($this->estilo_celda_actual)." ei-ml-fila-numero'>\n<span id='". $escapador->escapeHtmlAttr("{$this->objeto_js}_numerofila$fila")."'>". $escapador->escapeHtml($a + 1);
				echo "</span></td>\n";
			}			
			//--Layout de las filas
			$this->generar_layout_fila($fila);
			//--Numeración de las filas
			if ($this->_info_formulario['filas_ordenar'] && $this->_ordenar_en_linea) {
				echo "<td class='". $escapador->escapeHtmlAttr($this->estilo_celda_actual)." ei-ml-fila-ordenar'>\n";
				echo "<a href='javascript: ". $escapador->escapeHtmlAttr($this->objeto_js).".subir_seleccionada();' id='".$escapador->escapeHtmlAttr("{$this->objeto_js}_subir$fila")."' style='visibility:hidden' title='Subir la fila'>".
					toba_recurso::imagen_toba('nucleo/orden_subir.gif', true).'</a>';
				echo "<a href='javascript: ". $escapador->escapeHtmlAttr($this->objeto_js).".bajar_seleccionada();' id='". $escapador->escapeHtmlAttr("{$this->objeto_js}_bajar$fila") ."' style='visibility:hidden' title='Bajar la fila'>".
					toba_recurso::imagen_toba('nucleo/orden_bajar.gif', true).'</a>';
				echo "</td>\n";
			}			
			//--Creo los EVENTOS de la FILA
			$this->generar_eventos_fila($fila);			

			//-- Borrar a nivel de fila
			if ($this->_info_formulario['filas_agregar'] && $this->_borrar_en_linea && $es_fila_modelo) {		//Si no es la fila modelo, evito que se pueda eliminar
				echo "<td class='". $escapador->escapeHtmlAttr($this->estilo_celda_actual)." ei-ml-columna-evt ei-ml-fila-borrar'>";
				$cod_js = 'onclick="'. $escapador->escapeHtmlAttr($this->objeto_js).'.seleccionar('. $escapador->escapeHtmlAttr($fila).');'. $escapador->escapeHtmlAttr($this->objeto_js).'.eliminar_seleccionada();"';
				echo toba_form::button_html("{$this->objeto_js}_eliminar$fila", toba_recurso::imagen_toba('borrar.gif', true), 
										$cod_js, 
										$this->_rango_tabs[0]++, null, 'Elimina la fila');
				echo "</td>\n";									
			}
			
			echo "</tr>\n";
			$a++;
		}
		echo "</tbody>\n";		
	}
	
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		{$id_js}.crear_fila = function() 
		{			
			var nuevo_anexado = ei_formulario_ml.prototype.crear_fila.call(this);
			//Tengo que deshabilitar el boton de agregado de filas.
			this.boton_agregar().disabled = true;
		
			return nuevo_anexado;
		};
		
		{$id_js}.eliminar_fila = function(fila) 
		{			
			var anterior = ei_formulario_ml.prototype.eliminar_fila.call(this, fila);
			//Tengo que re habilitar el boton de agregado de filas.
			this.boton_agregar().disabled = false;
		
			return anterior;
		};		
		";		
	}

}

?>