<?php
require_once('seleccion_imagenes.php');

class eiform_abm_detalle extends toba_ei_formulario_ml
{
	private $fila_protegida;

	function set_fila_protegida($fila)
	{
		$this->fila_protegida = $fila;	
	}

	function generar_input_ef($ef)
	{
		if ($ef == 'imagen') {
			echo "<div class='editor-imagen-preview'>";
		}
		parent::generar_input_ef($ef);
		if ($ef == 'imagen') {
			$fila = $this->ef($ef)->get_fila_actual();
			$origen = $this->ef('imagen_recurso_origen')->get_estado();
			$img = $this->ef($ef)->get_estado();
			seleccion_imagenes::generar_input_ef($origen, $img, $this->objeto_js, $fila);			
		} 
		if ($ef == 'imagen') {
			echo '</div>';
		}
	}	

	function extender_objeto_js()
	{
		$escapador = toba::escaper();
		$id_js = $escapador->escapeJs($this->objeto_js);
		// La fila seleccionada no se puede eliminar
		if (isset($this->fila_protegida)) {
			echo "	{$id_js}.evt__baja = function (fila) {
					if( fila == ".$escapador->escapeJs($this->fila_protegida).") {
						alert('No es posible eliminar la columna que se esta editando');
						return false;
					}else{
						return true;
					}
				}
			";
		}
		//Si cambia el obligatorio muestra el relajado por oculto
		echo "
			{$id_js}.evt__obligatorio__procesar = function(inicial, fila_actual) {
				if (isset(this.ef('oculto_relaja_obligatorio'))) {
					if (this.ef('obligatorio').ir_a_fila(fila_actual).chequeado()) {
						this.ef('oculto_relaja_obligatorio').ir_a_fila(fila_actual).activar();
					} else {
						this.ef('oculto_relaja_obligatorio').ir_a_fila(fila_actual).desactivar();
					}
				}
			}
		";		
		
		//Si se selecciona uno implicito, deseleccionar el resto.
		//Tambien seteo el manejo de datos (un implicito sin datos no tiene sentido)
		echo "
			{$id_js}.evt__implicito__procesar = function(inicial, fila_actual) {
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
			{$id_js}.evt__en_botonera__procesar = function(inicial, fila_actual) {
				if (this.ef('en_botonera').ir_a_fila(fila_actual).chequeado()) {
					if(this.ef('sobre_fila')) {// Esta extension se usa en varios forms...
						if (this.ef('sobre_fila').ir_a_fila(fila_actual).chequeado()) {
							this.ef('sobre_fila').ir_a_fila(fila_actual).chequear(false);
						}
					}
					if(this.ef('es_seleccion_multiple')) {// Esta extension se usa en varios forms...
						if (this.ef('es_seleccion_multiple').ir_a_fila(fila_actual).chequeado()) {
							this.ef('es_seleccion_multiple').ir_a_fila(fila_actual).chequear(false);
						}
					}
				}
			}
		";
		// Si se selecciona SOBRE FILA, no puede esta EN BOTONERA
		echo "
			{$id_js}.evt__sobre_fila__procesar = function(inicial, fila_actual) {
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
		
		seleccion_imagenes::generar_js($this->objeto_js, true);
	}
}
?>