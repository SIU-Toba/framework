<?
/**
*	Interface grafica de usuario
*/
interface gui
{
	function titulo( $texto );
	function subtitulo( $texto );
	function mensaje( $texto );
	function error( $texto );
}

class mock_gui implements gui
{
	function titulo( $texto ){}
	function subtitulo( $texto ){}
	function mensaje( $texto ){}
	function error( $texto ){}
}
?>