<?php
class servicio
{

	function cronometro ($solicitud,$rnd=false)
	{
		if($rnd){
			$ventana = mt_rand(1,1000);
			$post = mt_rand(1,1000);
		}else{
			$ventana = 'benchmark';
		}
		global $solicitud;
		$html .= "<a href='javascript:benchmark_$post()'><img src='". $solicitud->vinculador->imagen_general("reloj2.gif")."' border='0' alt='Benchmark de\nla solicitud'></a>";
		$html .=
"<script language='javascript'>
function benchmark_$post(){
	pagina='".$solicitud->vinculador->generar_url("siu_servicios_cronometro",array("solicitud" => $solicitud))."';
	params='scrollbars=yes,dependent=yes,width=600,height=400';
   	if (!window.window2) {
   	    window2 = window.open(pagina ,'v$ventana',params);
    }
   	else {
   	    if (!window2.closed) {
			window2.location.href=pagina;
            window2.focus();
   	    }
       	else {
            window2 = window.open(pagina ,'v$ventana',params);
   	    }
    }	
}
</script>";
		return $html;
	}

//----------------------------------------------------------------------------------------

	function grafico ($grafico,$debug=false)
	//Llama al graficador para que genere un grafico
	{
		global $solicitud;
		if($debug){
			$grafico["debug"] = 1;
			//Truchada por si hay varios graficos, en realidad deberia pasar un ID unico
			//Tengo pocas posibilidades de hacer dos graficos con el mismo ID, pero es de cuarta
			$aleatorio = mt_rand(1,1000);
			return "<a href='javascript:debug_$aleatorio()'>DEBUG GRAFICO</a>
<script language='javascript'>
<!--
function debug_$aleatorio(){
	pagina='".$solicitud->vinculador->generar_url("siu_servicios_grafico",$grafico)."';
	params='scrollbars=yes,dependent=yes,width=600,height=300';
   	if (!window.window2) {
   	    window2 = window.open(pagina,'debug',params);
    }
   	else {
   	    if (!window2.closed) {
			window2.location.href=pagina;
            window2.focus();
   	    }
       	else {
            window2 = window.open(pagina,'debug',params);
   	    }
    }	
}
-->
</script>
";
		}else{
			//Devuelve el tag IMG con la referencia al grafico
			return "<img src='".$solicitud->vinculador->generar_url("siu_servicios_grafico",$grafico)."'>";
		}
	}

//----------------------------------------------------------------------------------------

	function avisar_timeout ()
	//Codigo de javascript que avisa cuando se esta por acabar una sesion
	//Y se redirecciona al server para matarla (Cuidando que realmente se
	//hay vencido el timeout, sino lo estoy renovando!)
	{

	
	}
}
//----------------------------------------------------------------------------------------
?>