<?php

class cuadro_origen extends objeto_ei_cuadro
{
	/*
		Modificacion en PHP a los vinculos de las FILAS
	*/
	function modificar_vinculo_fila__prueba($vinculo, $fila)
	{
		$vinculo->agregar_parametro( 'nota', $this->datos[$fila]['descripcion'] );
	}

	/*
		agregar parametros en PHP a un boton normal, ubicado en la botonore
	*/
	function modificar_vinculo__boton( $vinculo )
	{
		$vinculo->agregar_parametro( 'dia', 'lunes' );
		$vinculo->agregar_parametro( 'bebida', 'vino' );
	}

	/*
		agregar parametros en JS al mismo boton, aca pueden leerse valores
		ingresados en el cliente
	*/
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.modificar_vinculo__boton = function(id_vinculo){
				var parametros = { mes: 'octubre', estacion: 'primavera'};
				vinculador.agregar_parametros(id_vinculo, parametros);
		}";
	}
}
?>