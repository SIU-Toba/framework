<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_formulario extends toba_ei_formulario
{
	protected $cambiar_layout = false;
	
	function cambiar_layout()
	{
		$this->cambiar_layout = true;
	}
	
	protected function generar_layout()
	{
		if (! $this->cambiar_layout) {
			parent::generar_layout();
		} else {
			echo '<table>';
			$i = 0;
			foreach ($this->get_nombres_ef() as $ef) {
				$ultimo = ($i == $this->get_cantidad_efs());
				if ($i % 2 == 0) {
					echo '<tr>';
				}
				echo '<td>';
				
				//--- Llamada a la generacion estandar de un ef
				$this->generar_html_ef($ef);
				
				echo '</td>';
				$i++;			
				if ($i % 2 == 0 || $ultimo) {
					echo '</tr>';
				}		
			}		
			echo '</table>';
		}
	}
	
	protected function generar_layout_impresion()
	{
		if (! $this->cambiar_layout) {
			parent::generar_layout_impresion();
		} else {
			$escapador = toba::escaper();
			echo "<table class='". $escapador->escapeHtmlAttr($this->_estilos)."' width='". $escapador->escapeHtmlAttr($this->_info_formulario['ancho'])."'>";
			$i = 0;
			foreach ($this->get_nombres_ef() as $ef) {
				$ultimo = ($i == $this->get_cantidad_efs());
				if ($i % 2 == 0) {
					echo '<tr>';
				}
				
				//--- Llamada a la generacion estandar de un ef
				$this->generar_html_impresion_ef($ef);

				$i++;			
				if ($i % 2 == 0 || $ultimo) {
					echo '</tr>';
				}
			}
			echo '</table>';
		}
	}	

	
	function extender_objeto_js()
	{
		//Valida que dos campos no tengan valor simultáneamente
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__validar_datos = function() {
				if (this.ef('descripcion').valor() != '' && this.ef('otra_descripcion').valor() != '' ) {
						notificacion.agregar('Sólo puede ingresar una descripción.');
						return false;
				}
				return true;
			}
		";
			
		//Agrega una confirmación al cancelar
		echo "
			{$id_js}.evt__cancelar = function() {
				return confirm('¿Esta seguro?');
			}
		";
			
		//Activa un campo en base a un checkbox
		echo "
			{$id_js}.evt__elige_tipo__procesar = function(es_inicial) {
				if (this.ef('elige_tipo').chequeado())
					this.ef('tipo').activar();
				else
					this.ef('tipo').desactivar();			
			}
			
			{$id_js}.evt__oculta_tipo__procesar = function(es_inicial) {
				if (this.ef('oculta_tipo').chequeado())
					this.ef('tipo').ocultar();
				else
					this.ef('tipo').mostrar();			
			}
		";
	}	
	
}

?>