<?php

/**
 * Clase con validaciones estáticas de datos
 * @package Centrales
 */
class toba_validaciones{
	
	/**
	 * Determina si un número de CBU (Clave Bancaria Uniforme) es válido
	 *
	 * @param string $cbu cadena que contiene el cbu a validar.
	 * @return true o false según sea válido o no.
	 */
	static function cbu_valido($cbu) {	
		if ( strlen($cbu) <> 22 ) {
			return false;
		} else {
			$digitos_cbu = str_split($cbu);
			for ($i = 0; $i <= 21; $i++) {
				if ( !is_numeric($digitos_cbu[$i])) {
					return false;
				}
			}
			if ($digitos_cbu[7]  != self::verificador($digitos_cbu, 0, 6)) {
				return false;
			}
			if ($digitos_cbu[21] != self::verificador($digitos_cbu, 8, 20)) {
				return false;
			}
			return true;
		}
	}
	
	/**
	 * Devuelve el dígito verificador para los dígitos de las posiciones "pos_inicial" a "pos_final" 
	 * de la cadena "$numero" usando clave 10 con ponderador 9713
	 *
	 * @param array $numero arreglo de digitos
	 * @param integer $pos_inicial
	 * @param integer $pos_final 
	 * @return integer digito verificador de la cadena $numero
	 */
	private function verificador($numero, $pos_inicial, $pos_final){
		$ponderador = array(3, 1, 7, 9);
		$suma = 0;
		$j = 0;
		for ($i = $pos_final; $i >= $pos_inicial; $i--){
			$suma = $suma + ($numero[$i] * $ponderador[$j % 4]);
			$j++;
		}
		$digito = (10 - $suma % 10) % 10;
		return $digito;
	}

}

?>