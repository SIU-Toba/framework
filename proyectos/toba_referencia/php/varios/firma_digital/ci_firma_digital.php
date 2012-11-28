<?php
class ci_firma_digital extends toba_ci
{
	protected $s__pdf;
	protected $s__datos_juegos;
	protected $s__datos_deportes;
	
	function generar_pdf()
	{
		require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');
		
		$pdf = new Cezpdf();
		$pdf->selectFont(toba_dir() . '/php/3ros/ezpdf/fonts/Helvetica.afm');
		$pdf->ezText('Ejemplo Firma Digital. Tiene dos attachments XML', 14);
		
		//-- Cuadro con datos
		$opciones = array(
				'splitRows'=>0,
				'rowGap' => 1,
				'showHeadings' => true,
				'titleFontSize' => 9,
				'fontSize' => 10,
				'shadeCol' => array(0.9,0.9,0.9),
				'outerLineThickness' => 0.7,
				'innerLineThickness' => 0.7,
				'xOrientation' => 'center',
				'width' => 500
		);
		$this->s__datos_juegos = toba::db()->consultar("SELECT * from ref_juegos");
		$pdf->ezTable($this->s__datos_juegos, '', 'Juegos', $opciones);
		$this->s__datos_deportes = toba::db()->consultar("SELECT * from ref_deportes");
		$pdf->ezTable($this->s__datos_deportes, '', 'Deportes', $opciones);
		
		$tmp = $pdf->ezOutput(0);
		$this->s__pdf = toba::proyecto()->get_www_temp("documento.pdf");
		if (! file_put_contents($this->s__pdf['path'], $tmp)) {
			throw new toba_error("Imposible escribir en '$archivo'. Chequee permisos");
		} 	
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function evt__firmar()
	{
		//Genero PDF
		$this->generar_pdf();
		
		//Genero XMLs
		$xml_juegos = toba::proyecto()->get_path_temp().'/entrada_juegos.xml';
		$xml = new toba_xml_tablas();
		$xml->set_tablas($this->s__datos_juegos, "juegos");
		$xml->guardar($xml_juegos);
		
		$xml_deportes = toba::proyecto()->get_path_temp().'/entrada_deportes.xml';
		$xml = new toba_xml_tablas();
		$xml->set_tablas($this->s__datos_deportes, "deportes");
		$xml->guardar($xml_deportes);
		
		//Agrego XMLs a PDF
		toba_firma_digital::pdf_add_attachments($this->s__pdf['path'], array($xml_juegos, $xml_deportes));

		$this->set_pantalla("pant_validacion");
	}
	
	function conf__pant_validacion()
	{
		$this->pantalla()->set_descripcion("<a href='".$this->s__pdf['url']."'>PDF Generado. Haga click derecho sobre este enlace para guardarlo en disco o visualizarlo localmente</a>");			
	}

	function evt__validar()
	{
		$ok = $this->validar_xmls();
		$certificado = file_get_contents(toba::nucleo()->toba_instalacion_dir()."/i__desarrollo/p__toba_referencia/publica.crt");
		$ok = $ok && $this->validar_certificado($certificado);
		if ($ok) {
			toba::notificacion()->agregar("Validación OK", "info");
		}
	}
	
	function validar_xmls()
	{
		$attachments = toba_firma_digital::pdf_get_attachments($this->s__pdf['path']);
		$ok = count($attachments) == 2;
		$carpeta = null;
		if ($ok && file_get_contents($attachments[0]) != file_get_contents(toba::proyecto()->get_path_temp().'/entrada_deportes.xml')) {
			$ok = false;
			toba::notificacion()->agregar("XML deportes modificado", "error");
		}
		if ($ok && file_get_contents($attachments[1]) != file_get_contents(toba::proyecto()->get_path_temp().'/entrada_juegos.xml')) {
						toba::notificacion()->agregar("XML juegos modificado", "error");
			$ok = false;
		}
		foreach ($attachments as $xml) {
			$carpeta = dirname($xml);
		}
		if (isset($carpeta)) {
			toba_manejador_archivos::eliminar_directorio($carpeta);
		}
		return $ok;
	}
	
	function validar_certificado($certificado)
	{
		toba_firma_digital::certificado_validar_expiracion($certificado);
		
		//Validación ONTI
		toba_firma_digital::certificado_validar_revocacion($certificado, dirname(__FILE__).'/onti.crl');
		//Validación RAIZ
		toba_firma_digital::certificado_validar_revocacion($certificado, dirname(__FILE__).'/raiz.crl');
		return true;
	}

}
?>