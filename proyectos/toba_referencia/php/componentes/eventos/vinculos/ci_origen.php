<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_origen extends toba_ci
{
	function conf()
	{
		//Modifico un vinculo en la botonera del cuadro.
		$vinculo_cuadro = $this->dep('cuadro')->evento('en_botonera')->vinculo();
		$vinculo_cuadro->agregar_parametro('nota', 'Este parametro se agrego en PHP');
		$vinculo_cuadro->agregar_parametro('dia', 'lunes');
		//Modifico un vinculo en un boton propio
		$vinculo_propio = $this->evento('abrir_en_ventana')->vinculo();
		$vinculo_propio->agregar_parametro('nota', 'Este parametro se agrego en PHP');
		$vinculo_propio->agregar_parametro('dia', 'miercoles');
	}

	/*
		Modificacion de un vinculo en JS
	*/
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		. ".modificar_vinculo__abrir_en_popup = function(id_vinculo){
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

	function conf__cuadro()
	{
		$datos[0]['id'] = 3;		
		$datos[0]['id2'] = 1;		
		$datos[0]['descripcion'] = 'Esta es la fila 1';
		$datos[0]['valor_a'] = 1000;
		$datos[0]['valor_b'] = 600;
		$datos[1]['id'] = 2;		
		$datos[1]['id2'] = 1;		
		$datos[1]['descripcion'] = 'Esta es la fila 2';
		$datos[1]['valor_a'] = 200;
		$datos[1]['valor_b'] = 7000;
		$datos[2]['id'] = 1;		
		$datos[2]['id2'] = 2;		
		$datos[2]['descripcion'] = 'Esta es la fila 3';
		$datos[2]['valor_a'] = 300;
		$datos[2]['valor_b'] = 5000;
		return $datos;
	}
}
?>