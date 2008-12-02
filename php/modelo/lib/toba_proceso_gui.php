<?php
/**
*	Interface grafica de usuario
*/
interface toba_proceso_gui
{
	function titulo( $texto );
	function subtitulo( $texto );
	function mensaje( $texto );
	function error( $texto );
	function progreso_avanzar();
	function enter();
}

class toba_mock_proceso_gui implements toba_proceso_gui
{
	function titulo( $texto ){}
	function subtitulo( $texto ){}
	function mensaje( $texto ){}
	function error( $texto ){}
	function progreso_avanzar() {}
	function progreso_fin() {}
	function enter(){}
	function cerrar(){}
	function lista() {}
	function separador() {}
}
?>