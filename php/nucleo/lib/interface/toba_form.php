<?php
/**
 * Clase estática con métodos que encapsulan los tags de un formulario HTML
 * Estos métodos son de bajisimo nivel y son solo shorcuts para evitar escribir html directo
 * 
 * @package SalidaGrafica
 */
class toba_form 
{

    static function text($nombre,$actual,$read_only,$len,$size,$clase="ef-input",$extra="")
    // EditBox
    {
    	$actual = texto_plano($actual);
    	$max_length = ($len != '') ? "maxlength='$len'" : '';
        $r = "<INPUT type='text' name='$nombre' id='$nombre' $max_length size='$size' ";
        if (isset($actual)) $r .= "value='$actual' ";
        if ($read_only) {
        	$r .= " readonly ";
        	$clase .= ' ef-input-solo-lectura';
        }
        $r .= "class='$clase' $extra />\n";
        return $r;
    }

    static function select($nombre,$actual,$datos,$clase="ef-combo", $extra="", $categorias=null)
    //Combo STANDART. recibe el listado en un array asociativo
    {
        if(!is_array($datos)) {
        	//Si datos no es un array, no puedo seguir
            $datos= array();
        }
        $combo = "<select name='$nombre' id='$nombre' class='$clase' $extra>\n";
		if (!isset($categorias)) {
	        foreach ($datos as $id => $desc){
				$s = ("$id" == "$actual") ? "selected" : "";
				$id = texto_plano($id);
				$desc = texto_plano($desc);
				$combo .= "<option value='$id' $s>$desc</option>\n";
	        }
		} else {
			foreach ($categorias as $categoria => $valores) {
				$combo .= "<optgroup label='$categoria'>\n";
				foreach ($valores as $id) {
		            $s = ($id == $actual) ? "selected" : "";
		            $desc = $datos[$id];
					$id = texto_plano($id);
					$desc = texto_plano($desc);
		            $combo .= "<option value='$id' $s>$desc</option>\n";
				}
				$combo .= "</optgroup>\n";
			}
		}
        $combo .= "</select>\n";
        return $combo;
    }
	
    static function multi_select($nombre,$actuales,$datos, $tamanio, $clase="ef-combo", $extra="")
    {
        if(!is_array($datos)){//Si datos no es un array, no puedo seguir
            $datos[""] = "";
        }
        $combo = "<select name='".$nombre."[]' id='$nombre' class='$clase' size='$tamanio' multiple $extra>\n";
        foreach ($datos as $id => $desc){
            $s = (in_array($id, $actuales)) ? "selected" : "";
            $id = texto_plano($id);
            $desc = texto_plano($desc);
            $combo .= "<option value='$id' $s>$desc</option>\n";
        }
        $combo .= "</select>\n";
        return $combo;
    }	

    static function textarea($nombre,$valor,$filas,$columnas,$clase="ef-textarea",$wrap="",$extra="")
    //TEXTAREA
//wrap=virtual
    {
        if(trim($wrap)!="") $wrap = "wrap='$wrap'";
        $valor = texto_plano($valor);
        return "<textarea class='$clase' name='$nombre' id='$nombre' rows='$filas' cols='$columnas' $wrap $extra>$valor</textarea>\n";
    }

    static function checkbox($nombre,$actual,$valor,$clase="ef-checkbox",$extra="")
    //Checkbox STANDART. recibe el valor y el valor actual
    {
        $s = ""; 
        if(!is_null($valor) && !is_null($actual) && $valor == $actual) $s = "CHECKED";		//Castea null a cero, por ende en necesario evitarlo
        $valor = texto_plano($valor);
        return "<input name='$nombre' id='$nombre' type='checkbox' value='$valor' $s class='$clase' $extra />\n";
    }

    static function radio($nombre, $actual, $datos, $clase=null, $extra="", $tab_index = "")
    {
    	if (!is_array($datos)) {
    		$datos = array();	
    	}
    	$html = '';
    	$html_clase = isset($clase) ? "class='$clase'" : '';
		$i=0;
    	foreach ($datos as $clave => $valor) {
    		$id = $nombre . $i;
    		$sel = ($actual == $clave) ? "checked" : "";
    		$clave = texto_plano($clave);
    		$valor = texto_plano($valor);
    		$html .= self::radio_manual($id, $nombre, $clave, $sel, $html_clase, $extra, $tab_index, $valor);
    		$tab_index = '';
    		$i++;
    	}
		return $html;
    }

	static function radio_manual($id, $nombre, $clave, $sel, $html_clase, $extra, $tab_index, $valor)
	{
		return  "<label class='ef-radio' for='$id'><input type='radio' id='$id' name='$nombre' value='$clave' $sel $html_clase $extra $tab_index />$valor</label>\n";
	}

    static function hidden($nombre,$valor, $extra="")
    //Campo HIDDEN
    {
    	$valor = texto_plano($valor);
        return "<input name='$nombre' id='$nombre' type='hidden' value='$valor' $extra />\n";
    }

    static function submit($nombre,$valor,$clase="ei-boton",$extra="", $tecla = null)
    // Boton de SUBMIT
    {
		if ($tecla === null)
	        return "<INPUT type='submit' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra />\n";
		else
			return toba_form::button_html($nombre, $valor, $extra, 0, $tecla, '', 'submit', '', $clase);
    }

    static function image($nombre,$src,$extra="", $tecla = null)
    // Boton de SUBMIT
    {
		$acceso = toba_recurso::ayuda($tecla);
        return "<INPUT type='image' name='$nombre' id='$nombre' src='$src' $acceso $extra />\n";
    }

    static function button($nombre,$valor,$extra="",$clase="ei-boton", $tecla = null)
    // Boton de SUBMIT
    {
		if ($tecla === null)
	        return "<INPUT type='button' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra />\n";
		else
			return toba_form::button_html($nombre, $valor, $extra, 0, $tecla, '', 'button', '', $clase);
    }

    static function button_html($nombre,$html, $extra="", $tab = null, $tecla = null, $tip='', $tipo='button', $valor='', 
    								$clase="ei-boton", $con_id=true, $estilo_inline=null, $habilitado=true )
    // Boton con html embebido
    {
		$acceso = toba_recurso::ayuda($tecla, $tip, $clase);
		$tab = (isset($tab) && $tab != 0) ? "tabindex='$tab'" : "";
		$id = ($con_id) ? "id='$nombre'" : '';
		$estilo_inline = isset($estilo_inline) ? "style='" . $estilo_inline . "'": '';
		$habilitado = $habilitado ? '' : 'DISABLED';
        return  "<button type='$tipo' name='$nombre' $id value='$valor' $tab $acceso $extra $estilo_inline $habilitado>".
				"<span>$html</span></button>\n";
    }

    static function password($nombre,$valor="", $maximo='', $tamanio='', $clase="ef-input", $extra = '')
    {
    	$valor = texto_plano($valor);
    	$max_length = ($maximo != '') ? "maxlength='$maximo'" : '';    	
    	$tamanio = ($tamanio != '') ? "size='$tamanio'" : '';  
        return "<INPUT type='password' name='$nombre' $tamanio $max_length id='$nombre' value='$valor' class='$clase' $extra />\n";
    }

    static function archivo($nombre,$valor=null,$clase="ef-upload",$extra="")
    // Boton de SUBMIT
    {
        if(isset($valor)) $valor = "value='$valor'";
        return "<INPUT type='file' name='$nombre' id='$nombre' $valor $extra class='$clase' />\n";
    }

    static function abrir($nombre,$action,$extra="",$method="post",$upload=true)
    {
        // Dejo el upload por defecto, asi no tengo que dejar una puerta para
        // cuando se necesita en los consumidores (particularmente el MT).
        // Aparentemente no tiene ningun efecto negativo...
        if($upload){
            $enctype="multipart/form-data";
            $method="post";//Post obligado en este caso
        }else{
            $enctype="application/x-www-form-urlencoded";
        }
        return  "\n<form  enctype='$enctype' id='$nombre' name='$nombre' method='$method' action='$action' $extra>\n";
    }

    static function cerrar()
    {
        return  "\n</form>\n";
    }


}
?>