<?php
//namespace SIU\ManejadorReferencia\Componentes\Interfaz;

use SIU\InterfacesManejadorSalidaToba\Componentes\Interfaz\ICuadro;

class referencia_cuadro implements ICuadro{
	public function getInicioCorte($id,$tabla_datos_es_general,$nivel){
		$salida = '';
		if($tabla_datos_es_general){
			$class = ($nivel>0)? 'col-md-'.(13-$nivel):'row';
			$salida .=  "<div class='table-responsive $class'>";
			$salida .= "<table class='table table-condensed table-hover table-bordered'>";
		}
		return $salida;
	}
	
	public function getFinCorte($tabla_datos_es_general){
		$salida = '';
		if($tabla_datos_es_general){
			$salida .="</table>";
			$salida .="</div>";
		}
		return $salida;
	}
}
