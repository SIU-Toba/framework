<?php
require_once('nucleo/componentes/interface/toba_ei_formulario_ml.php'); 


class eiform_abm_detalle extends toba_ei_formulario_ml
{
	private $fila_protegida;

	function set_fila_protegida($fila)
	{
		$this->fila_protegida = $fila;	
	}

	function generar_input_ef($ef)
	{
		echo "<div class='editor-imagen-preview'>";
		parent::generar_input_ef($ef);
		if ($ef == 'imagen') {
			$fila = $this->elemento_formulario[$ef]->get_fila_actual();
			$predeterminada = toba_recurso::imagen_toba('image-missing-16.png', false);
			$origen = $this->elemento_formulario['imagen_recurso']->get_estado();
			$img = $this->elemento_formulario[$ef]->get_estado();
			if ($img != '') {
				if ($origen == 'apex') {
					$actual = toba_recurso::imagen_toba($img);
				} else {
					$actual = toba_recurso::url_proyecto(toba_editor::get_proyecto_cargado());
					var_dump($actual);
					if ($actual != '') {
						$actual .= '/';
					}
					$actual .= "img/$img";
				}
			} else {
				$actual = $predeterminada;	
			}
			echo "<img title='Elegir la imagen desde un listado' onclick='{$this->objeto_js}.elegir_imagen($fila)'
					 id='editor_imagen_src$fila' src='$actual' onError='this.src=\"$predeterminada\"'/>";
		} 
		echo "</div>";
	}	

	function extender_objeto_js()
	{
		// La fila seleccionada no se puede eliminar
		if(isset($this->fila_protegida)){
			echo "	{$this->objeto_js}.evt__baja = function (fila) {
					if( fila == {$this->fila_protegida}	){
						alert('No es posible eliminar la columna que se esta editando');
						return false;
					}else{
						return true;
					}
				}
			";
		}
		//Si se selecciona uno implicito, deseleccionar el resto.
		//Tambien seteo el manejo de datos (un implicito sin datos no tiene sentido)
		echo "
			{$this->objeto_js}.evt__implicito__procesar = function(inicial, fila_actual) {
				if (this.ef('implicito').ir_a_fila(fila_actual).chequeado()) {
					this.ef('maneja_datos').ir_a_fila(fila_actual).chequear();
					for (var id_fila in this._filas) {
						if (this._filas[id_fila] != fila_actual) {
							this.ef('implicito').ir_a_fila(this._filas[id_fila]).chequear(false);
						}
					}
				}
			}
		";
		// Si se selecciona EN BOTONERA, no puede ser SOBRE FILA
		echo "
			{$this->objeto_js}.evt__en_botonera__procesar = function(inicial, fila_actual) {
				if (this.ef('en_botonera').ir_a_fila(fila_actual).chequeado()) {
					if(this.ef('sobre_fila')) {// Esta extension se usa en varios forms...
						if (this.ef('sobre_fila').ir_a_fila(fila_actual).chequeado()) {
							this.ef('sobre_fila').ir_a_fila(fila_actual).chequear(false);
						}
					}
				}
			}
		";
		// Si se selecciona SOBRE FILA, no puede esta EN BOTONERA
		echo "
			{$this->objeto_js}.evt__sobre_fila__procesar = function(inicial, fila_actual) {
				if (this.ef('sobre_fila').ir_a_fila(fila_actual).chequeado()) {
					if (this.ef('en_botonera').ir_a_fila(fila_actual).chequeado()) {
						this.ef('en_botonera').ir_a_fila(fila_actual).chequear(false);
					}
				}
			}
		";
		

		//------------------------------------------------------------------------
		//-------------------------- PREVIEW DE IMAGENES --------------------------
		//------------------------------------------------------------------------
		
		echo "
			{$this->objeto_js}.evt__imagen_recurso__procesar = function(inicial, fila) {
				if (! inicial) {
					this.evt__imagen__procesar(inicial,fila);
				}
			}
			{$this->objeto_js}.evt__imagen__procesar = function(inicial, fila) {
				var imagen = this.ef('imagen').ir_a_fila(fila);
				if (inicial) {
					imagen.input().onkeyup = imagen.input().onblur;
				} else {
					var prefijo = '';
					if (this.ef('imagen_recurso').ir_a_fila(fila).get_estado() == 'apex') {
						prefijo = toba_alias + '/';
					} else {
						if (toba_proyecto_alias != '') {
							prefijo = toba_proyecto_alias + '/';
						}
					}
					var imagen_src = prefijo + 'img/' + imagen.get_estado();
					$('editor_imagen_src' + fila).src= imagen_src;
				}
			}		
		
			{$this->objeto_js}.elegir_imagen = function(fila) {
				var callback =
				{
				  success: this.respuesta_listado ,
				  failure: toba.error_comunicacion,
				  scope: this
				}
				this.fila_con_imagen = fila;				
				var parametros = {'imagen': this.ef('imagen').ir_a_fila(fila).get_estado(),
								  'imagen_recurso_origen': this.ef('imagen_recurso').ir_a_fila(fila).get_estado()  };
				var vinculo = vinculador.crear_autovinculo('ejecutar', parametros);
				conexion.asyncRequest('GET', vinculo, callback, null);
				return true;
			}
			
			{$this->objeto_js}.respuesta_listado = function(resp) {
				notificacion.mostrar_ventana_modal('Seleccione la imagen',
								 resp.responseText, 'Cerrar','400px');
				
			}
			
			function seleccionar_imagen(path) {
				overlay(true);
				var fila = {$this->objeto_js}.fila_con_imagen;
				{$this->objeto_js}.ef('imagen').ir_a_fila(fila).set_estado(path);
				{$this->objeto_js}.evt__imagen__procesar(false, fila);
			}			
		";
	}
}
?>