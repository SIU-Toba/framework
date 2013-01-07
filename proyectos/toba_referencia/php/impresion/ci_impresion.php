<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_impresion extends toba_ci
{
	function conf()
	{
		$this->pantalla()->set_modo_descripcion(false);
	}

	function vista_impresion( toba_impresion $salida )
	{
		$salida->titulo($this->get_nombre());
		$salida->mensaje('Nota: Este es el Principal');
		$this->dependencia('filtro')->vista_impresion($salida);
		$this->dependencia('cuadro')->vista_impresion($salida);
		$this->dependencia('formulario')->vista_impresion($salida);
		$salida->salto_pagina();
		$salida->mensaje('Nota: Esta es una copia');
		$this->dependencia('filtro')->vista_impresion($salida);
		$this->dependencia('cuadro')->vista_impresion($salida);
		$this->dependencia('formulario')->vista_impresion($salida);		
		$salida->salto_pagina();
		$salida->mensaje('Este es un formulario ML que esta en otra pagina');
		$this->dependencia('ml')->vista_impresion($salida);
	}
	
	function vista_pdf(toba_vista_pdf $salida)
	{
		//Cambio lo márgenes accediendo directamente a la librería PDF
		$pdf = $salida->get_pdf();
		$pdf->ezSetMargins(80, 50, 30, 30);	//top, bottom, left, right
				
		//Pie de página
		$formato = 'Página {PAGENUM} de {TOTALPAGENUM}';
		$pdf->ezStartPageNumbers(300, 20, 8, 'left', $formato, 1);	//x, y, size, pos, texto, pagina inicio

		//Inserto los componentes usando la API de toba_vista_pdf
		$salida->titulo($this->get_nombre());
		$salida->mensaje('Nota: Este es el Principal');
		$this->dependencia('filtro')->vista_pdf($salida);
		$this->dependencia('cuadro')->vista_pdf($salida);
		$this->dependencia('formulario')->vista_pdf($salida);
		$salida->salto_pagina();
		$salida->mensaje('Nota: Esta es una copia');
		$this->dependencia('filtro')->vista_pdf($salida);
		$this->dependencia('cuadro')->vista_pdf($salida);
		$this->dependencia('formulario')->vista_pdf($salida);		
		$salida->salto_pagina();
		$salida->mensaje('Este es un formulario ML que esta en otra pagina');
		$salida->separacion();
		$this->dependencia('ml')->vista_pdf($salida);
		
		//Encabezado
		$pdf = $salida->get_pdf();
		foreach ($pdf->ezPages as $pageNum=>$id){
			$pdf->reopenObject($id);
			$imagen = toba::proyecto()->get_path().'/www/img/logo_toba_siu.jpg';
			$pdf->addJpegFromFile($imagen, 50, 780, 141, 45);	//imagen, x, y, ancho, alto
          	$pdf->closeObject();		
		}		
		
	}
	
	function vista_excel(toba_vista_excel $salida)
	{
		$excel = $salida->get_excel();
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setTitle('Principal');
		
		$salida->titulo('Filtro', 3);
		$this->dependencia('filtro')->vista_excel($salida);
		
		$salida->separacion(2);
		$salida->titulo('Multilínea', 3);		
		$this->dependencia('ml')->vista_excel($salida);
		
		$salida->separacion(2);
		$this->dependencia('cuadro')->vista_excel($salida);
		
		$salida->separacion(2);
		$salida->titulo('Formulario', 2);
		$this->dependencia('formulario')->vista_excel($salida);
		
		$salida->crear_hoja('Copia');
		$excel->setActiveSheetIndex(1);
				
		$salida->titulo('Copia del filtro en hoja 2', 3);
		$this->dependencia('filtro')->vista_excel($salida);
		
		$salida->separacion(2);
		$salida->titulo('Multilínea', 3);
		$this->dependencia('ml')->vista_excel($salida);

		$salida->separacion(2);
		$this->dependencia('cuadro')->vista_excel($salida);

		$salida->separacion(2);
		$salida->titulo('Formulario', 2);
		$this->dependencia('formulario')->vista_excel($salida);

	}	
	
	function vista_xslfo(toba_vista_xslfo $vista) 
	{
		$vista->set_nombre_archivo("salida.pdf");
	}
	
	function get_popup($clave)
	{
		return 'Nombre';
	}

	function conf__cuadro(toba_ei_cuadro $cuadro)
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
		$cuadro->set_datos($datos);
	}

	function conf__filtro(toba_ei_filtro $filtro)
	{
		$datos['editable'] = array('condicion' => 'es_igual_a', 'valor' =>  'editable');
		$datos['combo'] = array('condicion' => 'es_igual_a', 'valor' => 'P');
		$datos['checkbox'] = array('condicion' => 'es_igual_a','valor' => 1);
		$datos['precio'] = array('condicion' => 'es_igual_a','valor' =>'227');
		$datos['numero'] = array('condicion' => 'es_igual_a','valor' => '11241512');
		$datos['lista'] = array('condicion' => 'en_conjunto', 'valor' => array('a', 'c'));
		$datos['popup'] = array('condicion' => 'es_igual_a','valor' => '1');
		$datos['fecha'] = array('condicion' => 'es_igual_a','valor' => '2007-07-07');
		$datos['cuit'] = array('condicion' => 'es_igual_a','valor' => '30-54666670-7'); 
		$filtro->set_datos($datos);
	}

	function conf__ml(toba_ei_formulario_ml $form)
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
		$form->set_datos($datos);
	}

	function conf__formulario(toba_ei_formulario $form)
	{
		$datos['editable'] = 'editable';
		$datos['combo'] = 'P';
		$datos['checkbox'] = 1;
		$datos['precio'] = '227';
		$datos['numero'] = '11241512';
		$datos['lista'] = array('a', 'c');
		$datos['popup'] = '1';
		$datos['fecha'] = '2007-07-07';
		$datos['cuit'] = '30546666707'; 
		$form->set_datos($datos);
	}
	
	function servicio__impreso_plano()
	{
		echo "Esta impresion es la confirmacion de que funciona el metodo 'Impreso Plano'";
	}
}
?>