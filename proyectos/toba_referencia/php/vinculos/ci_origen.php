<?php

class ci_origen extends objeto_ci
{
	/*
		Modificacion de un vinculo en PHP
	*/
	function modificar_vinculo__ventana( $vinculo )
	{
		$vinculo->agregar_parametro( 'dia', 'lunes' );
	}

	/*
		Modificacion de un vinculo en JS
	*/
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.modificar_vinculo__popup = function(id_vinculo){
			if( confirm('Ejemplo de intercepcion de vinculos en el cliente ' +
						'(Si se presiona \"OK\" se agregan parametros \"a\" y \"b\", sino se cancela el vinculo).') ) {
				var parametros = { a: 'param_js_a', b: 'param_js_b'};
				vinculador.agregar_parametros(id_vinculo, parametros);
				vinculador.activar_vinculo(id_vinculo);
			} else {
				vinculador.desactivar_vinculo(id_vinculo);
			}
		}";
	}

	//---- DEPENDENCIAS -------------------------------------------------------

	function evt__cuadro__carga()
	{
		$datos[0]['id'] = 3;		
		$datos[0]['id2'] = 1;		
		$datos[0]['descripcion'] = 'Esta es la fila 1';
		$datos[1]['id'] = 2;		
		$datos[1]['id2'] = 1;		
		$datos[1]['descripcion'] = 'Esta es la fila 2';
		$datos[2]['id'] = 1;		
		$datos[2]['id2'] = 2;		
		$datos[2]['descripcion'] = 'Esta es la fila 3';
		return $datos;
	}
}
?>