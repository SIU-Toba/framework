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
        if ($read_only) $r .= "onFocus='this.blur()' ";
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
            $s = "";
            if ($id == $actual) $s = "SELECTED";
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

    function hidden($nombre,$valor)
    //Campo HIDDEN
    {
        return "<input name='$nombre' id='$nombre' type='hidden' value='$valor'>\n";
    }
//________________________________________________________________________________________________________

    function submit($nombre,$valor,$clase="ef-boton",$extra="")
    // Boton de SUBMIT
    {
        return "<INPUT type='submit' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra>\n";
    }
//________________________________________________________________________________________________________

    function image($nombre,$src,$extra="")
    // Boton de SUBMIT
    {
        return "<INPUT type='image' name='$nombre' id='$nombre' src='$src' $extra>\n";
    }
 //________________________________________________________________________________________________________

    function button($nombre,$valor,$extra="",$clase="ef-boton")
    // Boton de SUBMIT
    {
        return "<INPUT type='button' name='$nombre' id='$nombre' value='$valor' class='$clase' $extra>\n";
    }
//________________________________________________________________________________________________________

    function password($nombre,$valor="",$clase="ef-input")
    // Boton de SUBMIT
    {
        return "<INPUT type='password' name='$nombre' id='$nombre' value='$valor' class='$clase'>\n";
    }
//________________________________________________________________________________________________________

    function archivo($nombre,$valor=null,$clase="ef-input-upload")
    // Boton de SUBMIT
    {
        if(isset($valor)) $valor = "value='$valor'";
        return "<INPUT type='file' name='$nombre' id='$nombre' $valor class='$clase'>\n";
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

}
?>