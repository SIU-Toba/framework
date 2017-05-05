<?php
class pant_firma_digital extends toba_ei_pantalla
{
	function generar_layout()
	{
		//-- Genera Applet
		$this->dep('firmador')->generar_html();
		
		
		//-- Genera selector de documentos
		$sesion = $this->dep('firmador')->generar_sesion();
		
		//$url_actual = $this->dep('firmador')->get_url_base_actual(). $_SERVER['REQUEST_URI'];
		
		//JS lo necesita sin encodear y Java encodeado...
		$escapador = toba::escaper();
		$url_pdf_base =  $this->dep('firmador')->get_url_enviar_pdf(false)."&codigo=" . $escapador->escapeUrl($sesion);
		$url_pdf_base_encodeado = $this->dep('firmador')->get_url_enviar_pdf(true)."&codigo=". $escapador->escapeUrl($sesion);

		echo "
			<style type='text/css'>
				#pdf {
					float: right; 
					border: 1px solid black; 
					height:800px; 
					width:600px; 
				}
			</style>

			<script type='text/javascript'>
			function toggleDocumento(source, agregar) {
				var ok = false;
				if (agregar) {
					ok = document.AppletFirmador.agregarDocumento(source.id, source.getAttribute('value_encodeado'));
				} else {
					ok = document.AppletFirmador.quitarDocumento(source.id);
				}
				if (ok === null) {
					alert('Hubo un problema al comunicarse con el Applet firmador, chequee que se haya habilitados los permisos de interconexión.');
					return false;
				} else if (ok == false) {
					source.checked = ! agregar;
					return false;
				} else if (ok == true) {
					source.checked = agregar;
					return false;
				}
			}

			function seleccionarTodos(source) {
				checkboxes = document.getElementsByName('documentos');
				for (var i=0 ;i < checkboxes.length; i++) {
				  toggleDocumento(checkboxes[i], source.checked);
				  checkboxes[i].checked = source.checked;
				}
			}

			function verDocumento(source) {".
				$escapador->escapeJs($this->objeto_js) .".dep('firmador').ver_pdf_inline(source.value);
			}	
			</script>
		";
		
		echo "<div id='listado' style='display: none;float: left; width: 220px; margin-left: 20px; height: 600px;overflow:scroll;'>
				<input id='todos' type='checkbox' onclick='seleccionarTodos(this)' /> 
				<label for='todos'>Seleccionar Todos/Ninguno</label>
			<br/><br/>
		";
		$cant_documentos = 68;
		for ($i = 1; $i <= $cant_documentos; $i++) {
			echo "	<input id='". $escapador->escapeHtmlAttr($i)."' 
						name='documentos' 
						type='checkbox' 
						onclick='toggleDocumento(this, this.checked)' 
						value='".$escapador->escapeHtmlAttr($url_pdf_base.'&id='. $escapador->escapeUrl($i))."' 
						value_encodeado='". $escapador->escapeHtmlAttr($url_pdf_base_encodeado."&id=". $escapador->escapeUrl($i))."'
					/> 
					<a href=\"javascript:verDocumento(document.getElementById('". $escapador->escapeHtmlAttr($i)."'))\">Documento ". $escapador->escapeHtml($i)."</a>
					<br/>";
		}
		echo "</div>";

	}

}
?>