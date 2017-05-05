<?php
php_referencia::instancia()->agregar(__FILE__);

class cuadro_origen extends toba_ei_cuadro
{
	/*
		Modificacion en PHP a los vinculos de las FILAS
	*/
	function conf_evt__en_fila_redefinido($evento, $fila)
	{
		$evento->vinculo()->agregar_parametro('nota', 'En PHP se agrego la columna _descripcion_ al paso de parametros ' .
											'(El ID del cuadro se incorpora por defecto). VALOR: ' . 
												$this->datos[$fila]['descripcion']);
	}

	
	function conf_evt__evt_valor_a($evento, $fila)
	{
		$vinculo = $evento->vinculo();
		$vinculo->set_id_ventana_popup('ventanaFija');
	}
	
	/*
		agregar parametros en JS al mismo boton, aca pueden leerse valores
		ingresados en el cliente
	*/
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js).".modificar_vinculo__en_botonera = function(id_vinculo){
				var parametros = { nota2: 'Esto se agrego en JAVASCRIPT', mes: 'octubre', estacion: 'primavera'};
				vinculador.agregar_parametros(id_vinculo, parametros);
		}";
	}
}
?>