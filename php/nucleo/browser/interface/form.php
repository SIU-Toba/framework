<?php
/******************************************************************************************************
****************************************** Elementos de Formulario ************************************
*****************************************************************************************************/

class form {
//Clase estatica implementa los elementos de formulario de HTML

    function text($nombre,$actual,$read_only,$len,$size,$clase="ef-input",$extra="")
    // EditBox
    {
        $r = "<INPUT type='text' name='$nombre' id='$nombre' maxlength='$len' size='$size' ";
        if (isset($actual)) $r .= "value='$actual' ";
        if ($read_only) $r .= " disabled ";
        $r .= "class='$clase' $extra>\n";
        return $r;
    }
//________________________________________________________________________________________________________

    function select($nombre,$actual,$datos,$clase="ef-combo", $extra="")
    //Combo STANDART. recibe el listado en un array asociativo
    {
        if(!is_array($datos)){//Si datos no es un array, no puedo seguir
            $datos[""]="";
        }
        $combo = "<select name='$nombre' id='$nombre' class='$clase' $extra>\n";
        foreach ($datos as $id => $desc){
            $s = ($id == $actual) ? "selected" : "";
            $combo .= "<option value='$id' $s>$desc</option>\n";
        }
        $combo .= "</select>\n";
        return $combo;
    }
	
    function multi_select($nombre,$actuales,$datos, $tamanio, $clase="ef-combo", $extra="")
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

    function textarea($nombre,$valor,$filas,$columnas,$clase="ef-textarea",$wrap="",$extra="")
    //TEXTAREA
//wrap=virtual
    {
        if(trim($wrap)!="") $wrap = "wrap='$wrap'";
        return "<textarea class='$clase' name='$nombre' id='$nombre' rows='$filas' cols='$columnas' $wrap $extra>$valor</textarea>\n";
    }
//________________________________________________________________________________________________________

    function checkbox($nombre,$actual,$valor,$clase="ef-checkbox",$extra="")
    //Checkbox STANDART. recibe el valor y el valor actual
    {
        $s = "";
        if($valor==$actual) $s = "CHECKED";
        return "<input name='$nombre' id='$nombre' type='checkbox' value='$valor' $s class='$clase' $extra>\n";
    }

//________________________________________________________________________________________________________

    function hidden($nombre,$valor, $extra="")
    //Campo HIDDEN
    {
        return "<input name='$nombre' id='$nombre' type='hidden' value='$valor' $extra>\n";
    }
//________________________________________________________________________________________________________

    function submit($nombre,$valor,$clase="ef-boton",$extra="", $tecla = null)
    // Boton de SUBMIT
    {
		if ($tecla === null)
	        return "<INPUT type='submit' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra>\n";
		else
			return form::button_html($nombre, $valor, $extra, 0, $tecla, '', 'submit', '', $clase);
    }
//________________________________________________________________________________________________________

    function image($nombre,$src,$extra="", $tecla = null)
    // Boton de SUBMIT
    {
		$acceso = form::acceso($tecla);
        return "<INPUT type='image' name='$nombre' id='$nombre' src='$src' $acceso $extra>\n";
    }
 //________________________________________________________________________________________________________

    function button($nombre,$valor,$extra="",$clase="ef-boton", $tecla = null)
    // Boton de SUBMIT
    {
		if ($tecla === null)
	        return "<INPUT type='button' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra>\n";
		else
			return form::button_html($nombre, $valor, $extra, 0, $tecla, '', 'button', '', $clase);
    }
//________________________________________________________________________________________________________

    function button_html($nombre,$html, $extra="", $tab = 0, $tecla = null, $tip='', $tipo='button', $valor='', $clase="ef-boton")
    // Boton con html embebido
    {
		$acceso = form::acceso($tecla, $tip);
        return  "<button type='$tipo' name='$nombre' id='$nombre' value='$valor' class='$clase' tabindex='$tab' $acceso $extra>".
				"$html</button>\n";
    }

//________________________________________________________________________________________________________
    function password($nombre,$valor="",$clase="ef-input")
    // Boton de SUBMIT
    {
        return "<INPUT type='password' name='$nombre' id='$nombre' value='$valor' class='$clase'>\n";
    }
//________________________________________________________________________________________________________

    function archivo($nombre,$valor=null,$clase="ef-input-upload",$extra="")
    // Boton de SUBMIT
    {
        if(isset($valor)) $valor = "value='$valor'";
        return "<INPUT type='file' name='$nombre' id='$nombre' $valor $extra class='$clase'>\n";
    }
//________________________________________________________________________________________________________

    function abrir($nombre,$action,$extra="",$method="POST",$upload=true)
    {
        // Dejo el upload por defecto, asi no tengo que dejar una puerta para
        // cuando se necesita en los consumidores (particularmente el MT).
        // Aparentemente no tiene ningun efecto negativo...
        if($upload){
            $enctype="multipart/form-data";
            $method="POST";//Post obligado en este caso
        }else{
            $enctype="application/x-www-form-urlencoded";
        }
        return  "\n<form  enctype='$enctype' name='$nombre' method='$method' action='$action' $extra style='margin-bottom:0;margin-top:0;'>\n";
    }
//________________________________________________________________________________________________________

    function cerrar()
    {
        return  "</form>\n";
    }

//________________________________________________________________________________________________________
	function acceso($tecla, $tip='')
	{
		return ($tecla === null) ? "title='$tip'" : "accesskey='$tecla' title=\"$tip [ALT $tecla]\"";		
	}

}
?>