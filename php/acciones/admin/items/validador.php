<?
/*
* controlar que junto a la imagen viene especificado un recurso
*/
	function validar_item_serv($registro)
	//Valida que la informacion que representa un ITEM sea valida en el SERVIDOR
	{
		$prefijo = "[validacion especifica ITEM] -";
		$faltas = array();
		//ei_arbol($registro,"DATOS recibidos por el VALIDADOR");
		//Controlo que la primera porcion del identificador del ITEM corresponda al
		//nombre de su PADRE.
		if($registro['padre']=="NULL"){
			$registro['padre']="";//Lo preparo para la comparacion
		}
		if( ((strpos($registro['item'],$registro['padre']."/")) === false) ||
			((strpos($registro['item'],$registro['padre']."/")) != 0 ) )
			{
				//echo "ITEM: '{$registro['item']}'<br>";
				//echo "PADRE: '{$registro['padre']}'<br>";
				$faltas[] = "$prefijo El nombre del ITEM debe tomar como base el nombre de su PADRE";
			}
		
		//Controlo que se setee una accion si no hay ningun PATRON ni BUFFER definido.
		if(	($registro["actividad_buffer_proyecto"] == "toba") &&
			($registro["actividad_buffer"] == "0") &&
			($registro["actividad_patron_proyecto"] == "toba")&&
			($registro["actividad_patron"] == "especifico")&& 
			((trim($registro["actividad_accion"]) == "")||(trim($registro["actividad_accion"]) == "NULL")) )
				$faltas[] = "$prefijo Es necesario definir una ACTIVIDAD (Patron, Buffer o Accion)";

		//Si hay MENU, tiene que haber MENU-ORDEN
		if(	($registro["menu"] == "1")&& 
			((trim($registro["orden"]) == "")||(trim($registro["orden"]) == "NULL")) )
				$faltas[] = "$prefijo Si incluye el ITEM en el menu, debe especificar un ORDEN del mismo";

		//-------------
		return $faltas;
	}
	
	function validar_inic_popup_serv($registro)
	//Valida : Si el link es un popup verifica que sea inicializado
	{
		$prefijo = "[validacion especifica VINCULADOR] -";
		$faltas  = array();
		//return ei_arbol($registro,"DATOS recibidos por el VALIDADOR");
		//Controlo que la primera porcion del identificador del ITEM corresponda al
		//nombre de su PADRE.
		if($registro['vinculo_tipo']=="popup"){
			if ($registro['inicializacion'] != "NULL"){
				$inic = explode(",",$registro['inicializacion']);
				if (count($inic) != 3)
					$faltas[] = "$prefijo Si el tipo de vinculo es Popup, debe completar el campo Inicializacion Popup como lo indica la ayuda (tamaño en X, tamaño en Y, scroll)";					
			}else
				$faltas[] = "$prefijo Si el tipo de vinculo es Popup, debe completar el campo Inicializacion Popup ";
		}
		return $faltas;
	}
	
	//----------------------------------------------------------------------

	function validar_item_cli($nombre_elementos)
	//Valida que la informacion que representa un ITEM sea valida en el CLIENTE
	//Al formulario me refiero como 'formulario'
	{
		//return "alert('Validador especifico: ".count($nombre_elementos)."')\n";
		return "
if((formulario.{$nombre_elementos[menu]}.checked)&&(formulario.{$nombre_elementos[orden]}.value == '')){
	alert('Si incluye el ITEM en el menu, tiene que establecer el ORDEN del mismo');
	formulario.{$nombre_elementos[orden]}.focus();
	return false;
}

if((formulario.{$nombre_elementos[patron]}.value=='toba".apex_ef_separador."especifico')
	&&(formulario.{$nombre_elementos[buffer]}.value == 'toba".apex_ef_separador."0')
	&& (formulario.{$nombre_elementos[accion]}.value == '') ){
	alert('Es necesario definir una ACTIVIDAD (PHP encargado del comportamiento del ITEM)');
	formulario.{$nombre_elementos[accion]}.focus();
	return false;
}
";
	}

	function validar_inic_popup_cli($nombre_elementos)
	//Valida : Si el link es un popup verifica que sea inicializado
	{
		return 
		"if(formulario.{$nombre_elementos[vinculo_tipo]}.value == 'popup'){
			if(formulario.{$nombre_elementos[inicializacion]}.value == ''){
				alert('Si el Tipo de Link es Popup, debe completar el campo Inicializacion POPUP');
				formulario.{$nombre_elementos[inicializacion]}.focus();
				return false;
			}
		}";
			
	}
	//----------------------------------------------------------------------
	/*VERLO!!!!!!!!!!!!!!!!!
				miArray = new Array(formulario.{$nombre_elementos[inicializacion]}.value);
				alert (miArray.length);
				if(miArray.length != 3){
					alert('Debe completar correctamente el campo Incializacion POPUP (Ver ayuda)');
					formulario.{$nombre_elementos[inicializacion]}.focus();
					return false;
	*/
?>