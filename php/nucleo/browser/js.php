<?
class js
//Clase para funciones javascript.
{
	function abrir()
	{
		return "<script language='JavaScript' type='text/javascript'>\n";
	}

	//-------------------------------------------------------------------------------------

	function cerrar()
	{
		return "</script>\n";
	}

	//-------------------------------------------------------------------------------------
	
	function cargar_consumos_globales($consumos)
	{
		$consumos = array_unique($consumos);
		
		foreach ($consumos as $consumo)	{
			switch ($consumo) {
				//--> Expresion regular que machea NULOS
				case 'ereg_nulo':
					echo "\n<script language='javascript'> ereg_nulo = /^\s*$/;</script>\n";
					break;
				//--> Expresion regular que machea NUMEROS
				case 'ereg_numero':
					echo "\n<script language='javascript'> ereg_numero = /^[1234567890,.-]*$/;</script>\n"; 
					break;
				//--> Codigo necesario para los ef_fecha
				case 'fecha':
					echo "\n\n<SCRIPT language='javascript' src='".recurso::js("calendario_es.js")."'></SCRIPT>\n";
					echo "\n\n<SCRIPT language='javascript' src='".recurso::js("validacion_fecha.js")."'></SCRIPT>\n";
					echo "<SCRIPT language='javascript'>document.write(getCalendarStyles());</SCRIPT>\n";
					echo "<SCRIPT language='javascript'>var calendario = new CalendarPopup('div_calendario');calendario.showYearNavigation();calendario.showYearNavigationInput();</SCRIPT>\n";
					echo "<DIV id='div_calendario'  style='VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white'></DIV>\n";
					break;
				//--> Codigo necesario para el EDITOR HTML embebido
				case 'fck_editor':
					echo "\n\n<SCRIPT type='text/javascript' src='".recurso::js("fckeditor/fckeditor.js")."'></SCRIPT>\n";
					break;
				//--> Por defecto carga el archivo del consumo
				default:
					echo "\n<SCRIPT language='javascript' src='".recurso::js("$consumo.js")."'></SCRIPT>\n";
	        }
		}
	}

}
?>