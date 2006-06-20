<?
include("nucleo/lib/buffer_i.php");

	$buffer =& new buffer_i("x","apex_objeto","instancia");
	$temp = $buffer->definicion;
	
	echo "<pre>";
	echo dump_array_php($temp);
	echo "</pre>";

	//ei_arbol($temp, "definicion");

?>