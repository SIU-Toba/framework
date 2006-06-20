<?

	if($editable = $this->zona->obtener_editable_propagado()){
		include_once("nucleo/browser/clases/objeto_grafico.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		$grafico =& new objeto_grafico($editable,$this);
		echo "<br>";
		$grafico->cargar_datos();
		$grafico->obtener_html();
		//$grafico->info_definicion();
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("El objeto solicitado no existe.","error");
	}

/*
//Testeo directo de la clase que provee funcionalidad.
 
 	$definicion["tipo"]="torta";
	$definicion["titulo"]="Cosas Utiles";
	$definicion["subtitulo"]="Resumen de cosas utiles al 10-4-2005";
	$definicion["alto"]=230;
	$definicion["ancho"]=300;
	$definicion["eje_a_nombre"]="Cosas Practicas";
	$definicion["eje_a"]=array('pies','brazos','errores','tuercas','zapatos','pelo');
	$definicion["eje_b_nombre"]="";
	$definicion["eje_b"]="";
	$definicion["serie"]=array(23,56,43,78,98,21);
	$definicion["debug"]=0;
	
	include_once("nucleo/browser/interface/grafico.php");
    $grafico =& new grafico();
	$grafico->cargar_definicion($definicion);
    $grafico->incrustar_imagen();
*/
?>