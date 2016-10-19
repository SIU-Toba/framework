<?php
class form_armado extends toba_ei_formulario
{
	function validar_estado()
	{
		return true;
	}
	
	function get_datos()
	{
		$registro = parent::get_datos();
		$ids_arbol = $this->controlador()->get_ids_enviados();
		foreach ($ids_arbol as $id) {
			if ( isset($_POST[$id.'__hidden'])) {
				$registro[$id] = $_POST[$id.'__hidden'];
			}
		}
		return $registro;
	}
	
	function generar_layout()
	{
		$this->generar_html_ef('nivel_inicial');
		echo '<div id="contenedor_final" class="menu">
				<ul style="list-style: none outside none;">&nbsp;</ul>
			</div>';		
	}
	
	protected function generar_input_ef($ef)
	{
		$this->_efs_generados[] = $ef;
		 $id = $this->_elemento_formulario[$ef]->get_id_form();
		echo toba_form::hidden($id, '');
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		//Envia los metodos que arman el arbol de items en JS.
		echo "
			var arbol = {};
			//Agrego funcion para eliminar un componente del objeto por su valor
			Array.prototype.removeByValue = function(val) {
				for(var i=0; i<this.length; i++) {
					if(this[i] == val) {
						this.splice(i, 1);
						break;
					}
				}
			}
			
			guardar_primer_nivel = function(indx) {		
				if (typeof arbol[indx] == 'undefined') {
					//Inicializo el nivel para el subarbol
					arbol[indx] = new Array();

					//Creo un hidden para guardar los valores a ser enviados
					var nomb_hid = indx + '__hidden';
					$('form').append($('<input/>', { type: 'hidden',
													 name: nomb_hid,														 
													 id: nomb_hid, 
													 value: ''}));
				}					
			}

			eliminar_primer_nivel = function (indx) {
				if (arbol[indx]) {								//Quito el subarbol completo
					delete(arbol[indx]);
					var nomb_hid = indx + '__hidden';			//Elimino el hidden para ese nivel
					$('#' + nomb_hid).detach();
				}
			}

			agregar_subnivel = function (padre, hijo)	{
				arbol[padre].push(hijo);
				eliminar_primer_nivel(hijo);
			}

			quitar_subnivel = function(padre, hijo) {			
				arbol[padre].removeByValue(hijo);
			}\n";
	}
}
?>