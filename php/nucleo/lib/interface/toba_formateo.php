<?php


/**
 * Funciones varias relacionadas con transformación de distintos formatos en diferentes medios
 * @package SalidaGrafica
 */
class toba_formateo
{
	protected $tipo_salida;
	
	function __construct($tipo_salida)
	{
		$this->tipo_salida = $tipo_salida;
	}
	
	protected function get_separador()
	{
		if ($this->tipo_salida == 'html') {
			return '&nbsp;';
		} else {
			return ' ';
		}		
	}

	function formato_escapar($valor)
	{
		return stripslashes($valor);
	}

	function formato_NULO($valor)
	{
		return $valor;
	}

	function formato_decimal($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return number_format($valor,2,',','.');
	}	

	function formato_porcentaje($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return number_format($valor,2,',','.') . $this->get_separador()."%";
	}	
	
	function formato_moneda($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return '$'.$this->get_separador(). number_format($valor,2,',','.');
	}

	function formato_tiempo($valor)
	{
		return "<b>" . number_format($valor,2,',','.') . '</b>'.
				$this->get_separador().'seg.';
	}

	function formato_millares($valor)
	{
		return number_format($valor,0,',','.');
	}
	
	function formato_numero($valor)
	{	
		return number_format($valor,2,',','.');
	}
	
	function formato_persona($valor)
	{
		return $valor . $this->get_separador()."p.";
	}

	function formato_mayusculas($valor)
	{
		return strtoupper($valor);
	}

	function formato_indivisible($valor)
	{
		if ($this->tipo_salida == 'html') {
			return "<span style='white-space:nowrap'>$valor</span>";
		} else {
			return $valor;
		}
	}
	
	function formato_may_ind($valor)
	{
		return str_replace (" ",$this->get_separador(),strtoupper(trim($valor)));
	}
	
	function formato_salto_linea_html($valor)
	{
		return  str_replace ("\n","<br />",$valor);
	}

	function formato_fecha($fecha){
	    if(isset($fecha)&&($fecha!='')){return cambiar_fecha($fecha,'-','/');} else {return '';};
	}
   
    function formato_checkbox($valor)
    {
        if ($valor === '1' || $valor === 1 || $valor === 'S' || $valor === 's')
            $html = "SI";
        else
            $html = "NO";
        return $html;
    }

	function formato_html_br($valor)
	{
		return ereg_replace("\n","<br>",$valor);
	}

	function formato_imagen_toba($valor)
	{
		return toba_recurso::imagen_toba($valor, true);
	}

	function formato_imagen_proyecto($valor)
	{
		return toba_recurso::imagen_proyecto($valor, true);
	}

	function formato_superficie($valor)
	{
		//Es trucho forzar desde aca, los datos tienen que esta bien
		//if($valor<0)$valor=0;
		return number_format($valor,2,',','.') . $this->get_separador() . "Km²";
	}	

	function formato_cuit($valor)
	{
		if (isset($valor) && $valor!='') {
			$length = strlen($valor);
			return substr($valor, 0, 2) . '-' . substr($valor, 2, $length - 3) . '-' . substr($valor, $length - 1, $length);
		} else
			return '';
	}
}
?>