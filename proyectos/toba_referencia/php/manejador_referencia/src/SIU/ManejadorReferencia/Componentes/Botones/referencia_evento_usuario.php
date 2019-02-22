<?php
//namespace SIU\ManejadorReferencia\Componentes\Botones;

use SIU\InterfacesManejadorSalidaToba\Componentes\Botones\IEventoUsuario;


class referencia_evento_usuario implements IEventoUsuario{

	public function getCss($esta_sobre_fila, $estilo, $defecto, $activado){
		// Si esta dentro de una celda, los alineo a derecha por una mejor visualizaci�n
		$clase_predeterminada = $esta_sobre_fila? '' : 'btn btn-default';
		
		if(!$activado)
			$clase_predeterminada .= " btn-disabled";
			
		if (isset($estilo) && (trim($estilo) == "") )
			return $clase_predeterminada;
		
		$estilo_definido = $estilo;
		
		$es_icono = strpos($estilo_definido, 'glyphicon-')!== false || strpos($estilo_definido, 'fa-')!== false; // se esta definiendo un icono a traves del estilo
		if ( $esta_sobre_fila ){
			
			$base = strpos($estilo_definido, 'glyphicon-')!== false ? 'glyphicon':'fa'; // es un icono de referencia o del template?
			if(!$activado)
				$base .= " btn-disabled";
				return "$base  $estilo_definido"; //Se inserta posteriomente un link
		}
		return "$clase_predeterminada $estilo_definido";
	}

	public function getTipoBoton($esta_sobre_fila, $estilo, $defecto){
		return \toba::output()->get('EventoUsuario',true)->getTipoBoton($esta_sobre_fila, $estilo, $defecto);
	}

	public function getImagen($imagen, $imagen_recurso_origen,$esta_sobre_fila,$estilo){
		
		if (isset($estilo) && $estilo!= '') {
			
			$estilo_definido = $estilo;
			$es_icono = strpos($estilo_definido, 'glyphicon-')!== false || strpos($estilo_definido, 'fa-')!== false; //es icono definido por estilo	
			if($es_icono){
				$base = strpos($estilo, 'glyphicon-')!== false ? 'glyphicon':'fa';
				return "<span class='$base $estilo_definido'></span>  ";
			}
		}
		return \toba::output()->get('EventoUsuario',true)->getImagen($imagen, $imagen_recurso_origen,$esta_sobre_fila,$estilo);
	}
	

	public function getInputButton($nombre, $html,$img="", $extra="", $tab = null, $tecla = null, $tip='', $tipo, $valor='',
			$clase, $con_id=true, $estilo_inline=null, $habilitado=true, $esta_sobre_fila = false){
		
		$html .= " $img";
		$clase = implode(array_filter(explode(' ',$clase),[$this, 'filtrado']),' ');
		//var_dump(array_filter(explode(' ',$clase),[$this, 'filtrado']));
		if($esta_sobre_fila){
			return \toba_form::link_html( $nombre, $html, $extra, $tab, $tecla, $tip, $tipo, '', $clase, true, $estilo_inline, $habilitado);
		}
		
		return \toba_form::button_html( $nombre, $html, $extra, $tab, $tecla,$tip, $tipo, '', $clase, true, $estilo_inline, $habilitado);
	}
	
	function filtrado($var){
		if (strpos(trim($var),"glyphicon-") !== FALSE || strpos(trim($var),"fa-") !== FALSE)
			return false;
		return true;
	}
	
	
	public function getInputCheckbox($nombre,$actual,$valor,$clase,$extra="",$img=""){
		$html = "<label>";
		$html .= $img;
		$html .=\toba_form::checkbox($nombre, $actual, $valor,$clase,$extra);
		$html .= "</label>";
		return $html;
	}
}
