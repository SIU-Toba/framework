<?php
class pant_firma extends toba_ei_pantalla
{
	function generar_layout()
	{
		$firmador = get_firmador();
		$url_firmador = toba_recurso::url_toba()."/firmador_pdf";
		$caracteres_invalidos = "||";

 		$url_descarga = toba::vinculador()->get_url(null, "30000064", array('accion' => "descargar"));
		$url_descarga = str_replace($caracteres_invalidos, urlencode($caracteres_invalidos), $url_descarga);
		$url_descarga = $firmador->get_url_base_actual().$url_descarga;
		
		$url_subir = toba::vinculador()->get_url(null, "30000064", array('accion' => "subir"));
		$url_subir = str_replace($caracteres_invalidos, urlencode($caracteres_invalidos), $url_subir);
		$url_subir = $firmador->get_url_base_actual().$url_subir;
		
		$sesion = $firmador->generar_sesion();
?>
        <applet  code="ar/gob/onti/firmador/view/FirmaApplet" 	 
           archive="<?php echo $url_firmador;?>/firmador.jar"  width="500"	height="310" >
			<param  name="URL_DESCARGA"	 value="<?php echo $url_descarga; ?>" >
			<param  name="URL_SUBIR"	value="<?php echo $url_subir; ?>">
			<param  name="MOTIVO"  value="Insertar motivo de la firma">
			<param  name="CODIGO"  value="<?php echo $sesion; ?>" />
			<param  name="PREGUNTAS" value='{ "preguntasRespuestas": []}' />
        </applet>
		<input type="hidden" value="<?php echo $sesion; ?>" name="firmador_codigo" />
<?
	}
	
	function extender_objeto_js() {
		echo "
			{$this->objeto_js}.desactivar_boton('finalizar');
				
			function firmaOk() {
				{$this->objeto_js}.activar_boton('finalizar');
			}
		";
		
	}

}
?>