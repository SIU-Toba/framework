<?
	if($editable = $this->zona->obtener_editable_propagado()){
		include_once("nucleo/browser/clases/objeto_hoja.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		$hoja =& new objeto_hoja($editable,$this);
		if($hoja->cargar_datos()===true){
			echo "<br>";
			//$hoja->info_definicion();
			$hoja->obtener_html();
			echo "<br>";
		}else{
			//echo ei_mensaje("ATENCION: Los datos no se cargaron");
			$hoja->mostrar_observaciones();		
		}
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("INSTANCIADOR de ABMS: No se explicito el objeto a a cargar","error");
	}
?>