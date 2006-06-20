<?
	if($editable = $this->zona->obtener_editable_propagado())
	{
		include_once("nucleo/browser/clases/objeto_ei_formulario_ml.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		$form =& new objeto_ei_formulario_ml($editable,$this);
		$form->cargar_datos();
		enter();
		echo "<div align='center'>\n";
		echo "<table class='objeto-base' width='100'>\n";
		echo "<tr><td>";
		$form->inicializar(array("nombre_formulario"=>"formulario", 'id' => 'id'));
		echo form::abrir("formulario", '');
		$consumo_js = array_unique($form->consumo_javascript_global());
		js::cargar_consumos_globales($consumo_js);
		echo form::cerrar();
		
		$form->obtener_html();
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</div>\n";

		enter();
		//dump_session();enter();
		$this->zona->obtener_html_barra_inferior();
		//dump_session();
	}else{
		echo ei_mensaje("INSTANCIADOR de ABMS: No se explicito el objeto a a cargar","error");
	}
?>