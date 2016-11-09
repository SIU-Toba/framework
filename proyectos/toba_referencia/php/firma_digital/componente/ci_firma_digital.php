<?php
class ci_firma_digital extends toba_ci
{
	protected $s__seleccion;
	protected $datos_persona;
	
	//---------------------------------------
	//----- Componente ei_firma
	//--------------------------------------
	
	function conf__firmador(toba_ei_firma $firmador)
	{
		$firmador->set_motivo_firma("Ejemplo de Toba");
		//$firmador->set_dimension("1000px", "100px");
		//$firmador->set_mostrar_pdf(false);	//Para ocultar la vista previa del documento
		
		//Se genera el PDF, así cuando lo viene a pedir el 'get_pdf' ya esta generado (mas facil ver los errores aca que en el evento)
		$this->generar_pdf();
	}
	
	/**
	 * Se envia el PDF sin firmar hacia el Applet
	 */
	function evt__firmador__enviar_pdf($token)
	{
		$this->get_datos_persona();
		$pdf = stream_get_contents($this->datos_persona['planilla_pdf']);
		return $pdf;
	}
	
	/**
	 * Se recibe el PDF firmado desde el Applet
	 */
	function evt__firmador__recibir_pdf_firmado($path, $token)
	{
		$this->guardar_pdf_en_tabla($path, true);
	}
	
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.desactivar_boton('finalizar');
				
			{$id_js}.dep('firmador').evt__firma_ok = function() {
				{$id_js}.activar_boton('finalizar');
				alert('LISTO! Al terminar la firma se puede atrapar el evt_firma_ok en javascript. En este caso se habilita el boton finalizar');
			}
		";
	}
	
	//---------------------------------------
	//----- Pegamento
	//--------------------------------------
	
	function evt__volver()
	{
		unset($this->s__seleccion);
		$this->set_pantalla("pant_listado");
	}
	
	function evt__finalizar()
	{
		unset($this->s__seleccion);
		$this->set_pantalla("pant_listado");
	}
	
	
	function conf__cuadro(toba_ei_cuadro $cuadro) 
	{
		$sql = "SELECT 
					id, 
					nombre, 
					planilla_pdf_firmada 
				FROM 
					ref_persona
				ORDER BY id ASC
		";
		$rs = toba::db()->consultar($sql);
		$cuadro->set_datos($rs);
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__seleccion = $seleccion;
		$this->set_pantalla("pant_firma");
	}
	
	function conf__pant_firma(toba_ei_pantalla $pant)
	{
		$this->get_datos_persona();
		if ($this->datos_persona['planilla_pdf_firmada']) {
			$pant->set_descripcion("La planilla PDF ya ha sido firmada correctamente");
			$pant->eliminar_dep("firmador");	//No mostrar firmador
		} else {
			$pant->set_descripcion($this->datos_persona['nombre']);
			
			//No mostrar eventos de borrar/descargar
			$pant->eliminar_evento('borrar_pdf');
			$pant->eliminar_evento('descargar_pdf');
		}
	}
	

	function evt__borrar_pdf()
	{
		$sql = "UPDATE ref_persona SET planilla_pdf = NULL, planilla_pdf_firmada = 0 WHERE id = ".quote($this->s__seleccion['id']);
		toba::db()->ejecutar($sql);		
		$this->evt__volver();
	}
	

	
	function get_datos_persona()
	{
		if (! isset($this->datos_persona)) {
			$sql = "SELECT
						id,
						nombre,
						fecha_nac,
						planilla_pdf,
						planilla_pdf_firmada
					FROM ref_persona WHERE id = ".quote($this->s__seleccion['id']);
			$this->datos_persona = toba::db()->consultar_fila($sql);
		}
		return $this->datos_persona;
	}
	
	
	function generar_pdf()
	{
		$datos = $this->get_datos_persona();
		$temp = rand();
		$datos_persona = array();
		$datos_persona['id'] = $datos['id'];
		$datos_persona['nombre'] = $datos['nombre'];
		$datos_persona['fecha_nac'] = $datos['fecha_nac'];
		
		//Generar PDF
		require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');
		$pdf = new Cezpdf();
		$pdf->selectFont(toba_dir() . '/php/3ros/ezpdf/fonts/Helvetica.afm');
		$pdf->ezText('Ejemplo Firma Digital. Tiene un attachment XML', 14);
		$pdf->ezText('');
		$pdf->ezText("ID: {$datos['id']}", 14);
		$pdf->ezText("Nombre: {$datos['nombre']}", 14);
		$pdf->ezText("Fecha Nacimiento: {$datos['fecha_nac']}", 14);

		//Guardarlo en un archivo temporal
		$pdf_persona = toba::proyecto()->get_path_temp()."/$temp.pdf";
		file_put_contents($pdf_persona, $pdf->ezOutput(0));
		
		//Generar XML
		$xml_persona = toba::proyecto()->get_path_temp()."/$temp.xml";
		$xml = new toba_xml_tablas();
		$xml->set_tablas($datos_persona, "persona");
		$xml->guardar($xml_persona);
		
		//Agrego XMLs a PDF
		toba_firma_digital::pdf_add_attachments($pdf_persona, array($xml_persona));
		
		//Actualizo tabla 
		$this->guardar_pdf_en_tabla($pdf_persona, false);
		
		//Retorno PDF y borro temporales
		$retorno = file_get_contents($pdf_persona);
		unlink($pdf_persona);
		unlink($xml_persona);
		return $retorno;
	}
	
	function guardar_pdf_en_tabla($path_pdf, $firmado)
	{
		$firmado = $firmado ? 1 : 0;
				$fp = fopen($path_pdf, 'rb');
		$sentencia = toba::db()->sentencia_preparar("UPDATE ref_persona SET planilla_pdf = ?, planilla_pdf_firmada = $firmado WHERE id = ".quote($this->s__seleccion['id']));
		toba::db()->sentencia_agregar_binarios($sentencia, array($fp));
		toba::db()->sentencia_ejecutar($sentencia);
		fclose($fp);
	}
	
	
	
}

?>