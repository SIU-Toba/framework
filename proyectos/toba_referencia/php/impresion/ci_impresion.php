<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_impresion extends toba_ci
{
	function conf()
	{
		$this->pantalla()->set_modo_descripcion(false);
	}

	function vista_impresion( $salida )
	{
		$salida->titulo( $this->get_nombre() );
		$salida->mensaje('Nota: Este es el Principal');
		$this->dependencia('filtro')->vista_impresion( $salida );
		$this->dependencia('cuadro')->vista_impresion( $salida );
		$salida->salto_pagina();
		$salida->mensaje('Nota: Esta es una copia');
		$this->dependencia('filtro')->vista_impresion( $salida );
		$this->dependencia('cuadro')->vista_impresion( $salida );
		$salida->salto_pagina();
		$salida->mensaje('Este es un formulario ML que esta en otra pagina');
		$this->dependencia('ml')->vista_impresion( $salida );
	}
	
	function vista_pdf(toba_vista_pdf $salida)
	{
		$salida->titulo( $this->get_nombre() );
		$salida->mensaje('Nota: Este es el Principal');
		$this->dependencia('filtro')->vista_pdf( $salida );
		$this->dependencia('cuadro')->vista_pdf( $salida );
		$salida->salto_pagina();
		$salida->mensaje('Nota: Esta es una copia');
		$this->dependencia('filtro')->vista_pdf( $salida );
		$this->dependencia('cuadro')->vista_pdf( $salida );
		$salida->salto_pagina();
		$salida->mensaje('Este es un formulario ML que esta en otra pagina');
		$salida->separacion();
		$this->dependencia('ml')->vista_pdf( $salida );
	}
	
	function vista_excel(toba_vista_excel $salida)
	{
		$excel = $salida->get_excel();
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setTitle('Principal');
		$salida->titulo('Filtro');
		$this->dependencia('filtro')->vista_excel($salida);
		$salida->separacion(1);
		$salida->titulo('Copia del filtro');
		$this->dependencia('filtro')->vista_excel($salida);
		
		$salida->crear_hoja('Copia');
		$salida->titulo('Copia del filtro en hoja 2');
		$this->dependencia('filtro')->vista_excel($salida);
		
		/*		$salida->titulo( $this->get_nombre() );
		
		$salida->mensaje('Nota: Este es el Principal');
		$this->dependencia('filtro')->vista_pdf( $salida );
		$this->dependencia('cuadro')->vista_pdf( $salida );
		$salida->salto_pagina();
		$salida->mensaje('Nota: Esta es una copia');
		$this->dependencia('filtro')->vista_pdf( $salida );
		$this->dependencia('cuadro')->vista_pdf( $salida );
		$salida->salto_pagina();
		$salida->mensaje('Este es un formulario ML que esta en otra pagina');
		$salida->separacion();
		$this->dependencia('ml')->vista_pdf( $salida );*/
	}	
	
	function get_popup($clave)
	{
		return 'Nombre';
	}

	function conf__cuadro()
	{
		$datos[0]['id'] = '1';
		$datos[0]['tipo'] = '1';
		$datos[0]['desc'] = 'Hola';
		$datos[1]['id'] = '2';
		$datos[1]['tipo'] = '1';
		$datos[1]['desc'] = 'Chau';
		$datos[2]['id'] = '3';
		$datos[2]['tipo'] = '1';
		$datos[2]['desc'] = 'Si';
		$datos[3]['id'] = '4';
		$datos[3]['tipo'] = '2';
		$datos[3]['desc'] = 'No';
		$datos[4]['id'] = '5';
		$datos[4]['tipo'] = '2';
		$datos[4]['desc'] = 'Mas';
		$datos[5]['id'] = '6';
		$datos[5]['tipo'] = '2';
		$datos[5]['desc'] = 'Menos';
		return $datos;
	}

	function conf__filtro()
	{
		$datos['editable'] = 'editable';
		$datos['combo'] = 'P';
		$datos['checkbox'] = '1';
		$datos['precio'] = '227';
		$datos['numero'] = '11241512';
		$datos['lista'] = array('a', 'c');
		$datos['popup'] = '1';
		$datos['fecha'] = '2007-7-7';
		$datos['cuit'] = '20055121711';
		return $datos;
	}

	function conf__ml()
	{
		$datos[0]['id'] = '1';
		$datos[0]['tipo'] = '1';
		$datos[0]['desc'] = 'Hola';
		$datos[1]['id'] = '2';
		$datos[1]['tipo'] = '1';
		$datos[1]['desc'] = 'Chau';
		$datos[2]['id'] = '3';
		$datos[2]['tipo'] = '1';
		$datos[2]['desc'] = 'Si';
		$datos[3]['id'] = '4';
		$datos[3]['tipo'] = '2';
		$datos[3]['desc'] = 'No';
		$datos[4]['id'] = '5';
		$datos[4]['tipo'] = '2';
		$datos[4]['desc'] = 'Mas';
		$datos[5]['id'] = '6';
		$datos[5]['tipo'] = '2';
		$datos[5]['desc'] = 'Menos';
		return $datos;
	}
}
?>