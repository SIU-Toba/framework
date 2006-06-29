<?php
require_once("nucleo/lib/recurso.php");

/******************************************************************************************************
****************************************** Elementos de Formulario ************************************
*****************************************************************************************************/

class form {
//Clase estatica implementa los elementos de formulario de HTML

    static function text($nombre,$actual,$read_only,$len,$size,$clase="ef-input",$extra="")
    // EditBox
    {
    	$max_length = ($len != '') ? "maxlength='$len'" : '';
        $r = "<INPUT type='text' name='$nombre' id='$nombre' $max_length size='$size' ";
        if (isset($actual)) $r .= "value='$actual' ";
        if ($read_only) $r .= " readonly ";
        $r .= "class='$clase' $extra>\n";
        return $r;
    }
//________________________________________________________________________________________________________

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
				$combo .= "<option value='$id' $s>$desc</option>\n";
	        }
		} else {
			foreach ($categorias as $categoria => $valores) {
				$combo .= "<optgroup label='$categoria'>\n";
				foreach ($valores as $id) {
		            $s = ($id == $actual) ? "selected" : "";
		            $desc = $datos[$id];
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
            $combo .= "<option value='$id' $s>$desc</option>\n";
        }
        $combo .= "</select>\n";
        return $combo;
    }	

//________________________________________________________________________________________________________

    static function textarea($nombre,$valor,$filas,$columnas,$clase="ef-textarea",$wrap="",$extra="")
    //TEXTAREA
//wrap=virtual
    {
        if(trim($wrap)!="") $wrap = "wrap='$wrap'";
        return "<textarea class='$clase' name='$nombre' id='$nombre' rows='$filas' cols='$columnas' $wrap $extra>$valor</textarea>\n";
    }
//________________________________________________________________________________________________________

    static function checkbox($nombre,$actual,$valor,$clase="ef-checkbox",$extra="")
    //Checkbox STANDART. recibe el valor y el valor actual
    {
        $s = "";
        if($valor==$actual) $s = "CHECKED";
        return "<input name='$nombre' id='$nombre' type='checkbox' value='$valor' $s class='$clase' $extra>\n";
    }

//________________________________________________________________________________________________________    
    
    static function radio($nombre, $actual, $datos, $clase=null, $extra="")
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
    		$html .= "<label class='ef-radio' for='$id'><input type='radio' id='$id' name='$nombre' value='$clave' $sel $html_clase $extra />$valor</label>\n";
    		$i++;
    	}
		return $html;
    }
    
//________________________________________________________________________________________________________

    static function hidden($nombre,$valor, $extra="")
    //Campo HIDDEN
    {
        return "<input name='$nombre' id='$nombre' type='hidden' value='$valor' $extra>\n";
    }
//________________________________________________________________________________________________________

    static function submit($nombre,$valor,$clase="ef-boton",$extra="", $tecla = null)
    // Boton de SUBMIT
    {
		if ($tecla === null)
	        return "<INPUT type='submit' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra>\n";
		else
			return form::button_html($nombre, $valor, $extra, 0, $tecla, '', 'submit', '', $clase);
    }
//________________________________________________________________________________________________________

    static function image($nombre,$src,$extra="", $tecla = null)
    // Boton de SUBMIT
    {
		$acceso = recurso::ayuda($tecla);
        return "<INPUT type='image' name='$nombre' id='$nombre' src='$src' $acceso $extra>\n";
    }
 //________________________________________________________________________________________________________

    static function button($nombre,$valor,$extra="",$clase="ef-boton", $tecla = null)
    // Boton de SUBMIT
    {
		if ($tecla === null)
	        return "<INPUT type='button' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra>\n";
		else
			return form::button_html($nombre, $valor, $extra, 0, $tecla, '', 'button', '', $clase);
    }
//________________________________________________________________________________________________________

    static function button_html($nombre,$html, $extra="", $tab = 0, $tecla = null, $tip='', $tipo='button', $valor='', $clase="ef-boton", $con_id=true)
    // Boton con html embebido
    {
		$acceso = recurso::ayuda($tecla, $tip, $clase);
		$tab = (isset($tab) && $tab != 0) ? "tabindex='$tab'" : "";
		$id = ($con_id) ? "id='$nombre'" : '';
        return  "<button type='$tipo' name='$nombre' $id value='$valor' $tab $acceso $extra>".
				"$html</button>\n";
    }

//________________________________________________________________________________________________________
    static function password($nombre,$valor="",$clase="ef-input")
    // Boton de SUBMIT
    {
        return "<INPUT type='password' name='$nombre' id='$nombre' value='$valor' class='$clase'>\n";
    }
//________________________________________________________________________________________________________

    static function archivo($nombre,$valor=null,$clase="ef-input-upload",$extra="")
    // Boton de SUBMIT
    {
        if(isset($valor)) $valor = "value='$valor'";
        return "<INPUT type='file' name='$nombre' id='$nombre' $valor $extra class='$clase'>\n";
    }
//________________________________________________________________________________________________________

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
        return  "\n<form  enctype='$enctype' name='$nombre' method='$method' action='$action' $extra>\n";
    }
//________________________________________________________________________________________________________

    static function cerrar()
    {
        return  "\n</form>\n";
    }

//________________________________________________________________________________________________________



}
?>