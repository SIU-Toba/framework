<?php
require_once('nucleo/browser/clases/objeto_ei_formulario.php');

class extension_formulario extends objeto_ei_formulario
{
	/*
	*	Se le agrega un evento particular 'mi_accion' y uno cancelar
	*/
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		unset($eventos['mi_evento']);
		$mi_accion = eventos::duplicar(eventos::alta(), 'mi_accion');
		$mi_accion['mi_accion']['etiqueta'] = 'Mi acción';
		$mi_accion['mi_accion']['imagen'] = recurso::imagen_apl('objetos/fantasma.gif');
		$eventos += $mi_accion;
		$eventos += eventos::cancelar();
		return $eventos;
	}
	

	function extender_objeto_js()
	{
		//Valida que dos campos no tengan valor simultáneamente
		echo "
			{$this->objeto_js}.evt__validar_datos = function() {
				if (this.ef('descripcion').valor() != '' && this.ef('otra_descripcion').valor() != '' ) {
						cola_mensajes.agregar('Sólo puede ingresar una descripción.');
						return false;
				}
				return true;
			}
		";
			
		//Agrega una confirmación al cancelar
		echo "
			{$this->objeto_js}.evt__cancelar = function() {
				return confirm('¿Esta seguro?');
			}
		";
			
		//Activa un campo en base a un checkbox
		echo "
			{$this->objeto_js}.evt__elige_tipo__procesar = function(es_inicial) {
				if (this.ef('elige_tipo').chequeado())
					this.ef('tipo').activar();
				else
					this.ef('tipo').desactivar();			
			}
			
			{$this->objeto_js}.evt__oculta_tipo__procesar = function(es_inicial) {
				if (this.ef('oculta_tipo').chequeado())
					this.ef('tipo').ocultar();
				else
					this.ef('tipo').mostrar();			
			}
		";
	}	
	
	

}

?>