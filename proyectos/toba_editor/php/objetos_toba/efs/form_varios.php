<?php 
class form_varios extends toba_ei_formulario
{

	function generar_input_ef($ef)
	{
		switch ($ef) {
			case 'edit_expreg':
				$expresiones = array(
					'mail' => 'e-mail',
					'cuit' => 'cuit',
					'hora' => 'hora',
					'id_valido' => 'id válido'
				);
				parent::generar_input_ef($ef);
				echo '<br>Validaciones: ';
				$inicial = '';
				foreach ($expresiones as $id => $desc) {
					echo "$inicial<a href='javascript: {$this->objeto_js}.pedir_expreg(\"$id\");'>$desc</a>";
					$inicial = ', ';
				}
				break;
			default:
				parent::generar_input_ef($ef);
		}
	}	
	
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
			
			{$this->objeto_js}.evt__popup_editable__procesar = function(es_inicial)
			{
				var cheq = !this.ef('popup_editable').chequeado();
				this.ef('popup_carga_desc_metodo').mostrar(cheq, true);
				this.ef('popup_carga_desc_estatico').mostrar(cheq, true);
				this.ef('popup_carga_desc_include').mostrar(cheq, true);
				this.ef('popup_carga_desc_clase').mostrar(cheq, true);	
				this.evt__popup_carga_desc_estatico__procesar(es_inicial);
			}

			{$this->objeto_js}.evt__popup_carga_desc_estatico__procesar = function(es_inicial)
			{
				var cheq = this.ef('popup_carga_desc_estatico').chequeado();
				this.ef('popup_carga_desc_include').mostrar(cheq, true);
				this.ef('popup_carga_desc_clase').mostrar(cheq, true);
				this.ef('punto_montaje').mostrar(cheq);
			}
			
			{$this->objeto_js}.pedir_expreg = function(tipo) {
				this.controlador.ajax('get_regexp', tipo, this, this.respuesta_expreg);
			}
			
			{$this->objeto_js}.respuesta_expreg = function(datos) {
				this.ef('edit_expreg').set_estado(datos);
			}

			{$this->objeto_js}.evt__punto_montaje__procesar = function(inicial) {
				if (!inicial) {
					this.ef('popup_carga_desc_include').cambiar_valor('');
					this.ef('popup_carga_desc_clase').cambiar_valor('');
				}
			}

			{$this->objeto_js}.evt__popup_carga_desc_include__procesar = function(inicial) {
				var archivo = this.ef('popup_carga_desc_include').valor();
				if (!inicial && this.ef('popup_carga_desc_clase').valor() == '') {
					var basename = archivo.replace( /.*\//, '' );
					var clase = basename.substring(0, basename.lastIndexOf('.'));
					this.ef('popup_carga_desc_clase').cambiar_valor(clase);
				}
			}

			{$this->objeto_js}.modificar_vinculo__ef_popup_carga_desc_include = function(id_vinculo)
            {
				var estado = this.ef('punto_montaje').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
            }

			{$this->objeto_js}.modificar_vinculo__extender = function(id_vinculo)
			{
				var estado = this.ef('punto_montaje').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
            }
		";
	}
}

?>