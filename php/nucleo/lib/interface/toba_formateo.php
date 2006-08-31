<?php

	function formato_escapar($valor)
	{
		return stripslashes($valor);
	}
/*----------------------------*/

	function formato_NULO($valor)
	{
		return $valor;
	}
/*----------------------------*/

	function formato_decimal($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return "&nbsp;" . number_format($valor,2,',','.');
	}	
/*----------------------------*/

	function formato_porcentaje($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return "&nbsp;" . number_format($valor,2,',','.') . "&nbsp;%";
	}	
/*----------------------------*/
	
	function formato_moneda($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return "$&nbsp;" . number_format($valor,2,',','.');
	}
/*----------------------------*/

	function formato_tiempo($valor)
	{
		return "<b>" . number_format($valor,2,',','.') . "</b>&nbsp;seg.";
	}
/*----------------------------*/

	function formato_millares($valor)
	{
		return "&nbsp;" . number_format($valor,0,',','.');
	}
/*----------------------------*/

	function formato_persona($valor)
	{
		return $valor . "&nbsp;p.";
	}
/*----------------------------*/

	function formato_mayusculas($valor)
	{
		return strtoupper($valor);
	}
/*----------------------------*/

	function formato_indivisible($valor)
	{
		return str_replace (" ","&nbsp;", trim($valor));
	}
/*----------------------------*/
	
	function formato_may_ind($valor)
	{
		return str_replace (" ","&nbsp;",strtoupper(trim($valor)));
	}
/*----------------------------*/
	
	function formato_salto_linea_html($valor)
	{
		return  str_replace ("\n","<br>",$valor);
	}
/*----------------------------*/

	function cambiar_fecha($fecha,$sep_actual,$sep_nuevo){
		if( isset( $fecha ) ) {
			$f = explode($sep_actual,$fecha);
			return $f[2] . $sep_nuevo . $f[1] . $sep_nuevo . $f[0];
		}
	}
/*----------------------------*/

	function formato_fecha($fecha){
	    if(isset($fecha)&&($fecha!='')){return cambiar_fecha($fecha,'-','/');} else {return '';};
	}
/*----------------------------*/
   
    function formato_checkbox($valor)
    {
        if ($valor == 1)
            $html = "SI";
        else
            $html = "NO";
        return $html;
    }
/*----------------------------*/

	function formato_html_br($valor)
	{
		return ereg_replace("\n","<br>",$valor);
	}
/*----------------------------*/
	function formato_imagen_toba($valor)
	{
		return toba_recurso::imagen_apl($valor, true);
	}
/*----------------------------*/
	function formato_imagen_proyecto($valor)
	{
		return toba_recurso::imagen_pro($valor, true);
	}
/*----------------------------*/

	function formato_superficie($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return "&nbsp;" . number_format($valor,2,',','.') . "&nbsp;" . "Km²";
	}	
/*----------------------------*/

?>