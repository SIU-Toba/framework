<?php
require_once('nucleo/componentes/interface/toba_ei_formulario_ml.php'); 


class eiform_abm_detalle extends toba_ei_formulario_ml
{
	private $fila_protegida;

	function set_fila_protegida($fila)
	{
		$this->fila_protegida = $fila;	
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
	}
}
?>