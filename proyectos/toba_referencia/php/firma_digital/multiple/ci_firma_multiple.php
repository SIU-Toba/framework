<?php
class ci_firma_multiple extends toba_ci
{
	protected $s__seleccion;
	protected $datos_persona;
	
	//---------------------------------------
	//----- Componente ei_firma
	//--------------------------------------
	
	function conf__firmador(toba_ei_firma $firmador)
	{
		$firmador->set_motivo_firma("Ejemplo de firma multiple en Toba");
		$firmador->set_multiple(true);
	}
	
	/**
	 * Se envia el PDF sin firmar hacia el Applet
	 */
	function evt__firmador__enviar_pdf($token, $numero)
	{
		$file = dirname(__FILE__).'/pdfs_sin_firmar/doc_'.$numero.'.pdf';
		$fd = fopen($file,'r');
		$pdf = stream_get_contents($fd);
		return $pdf;
	}
	
	/**
	 * Se recibe el PDF firmado desde el Applet
	 */
	function evt__firmador__recibir_pdf_firmado($path, $token, $numero)
	{
		$destino = dirname(__FILE__).'/pdfs_firmados/doc_'.$numero.'.pdf';
		if (! rename($path, $destino)) {
			throw new toba_error("No fue posible mover el archivo desde $path hacia $destino");
		}
	}
	
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.dep('firmador').evt__firma_ok = function() {
				document.getElementById('listado').style.display = 'none';
				this.agregar_notificacion('Documentos firmados y almacenados en la carpeta \"multiple/pdfs_firmados\"', 'info');				
			}			
			
			{$id_js}.dep('firmador').evt__applet_cargado = function() {
				document.getElementById('listado').style.display = '';
			}			
		";
	}
	

	
}

?>