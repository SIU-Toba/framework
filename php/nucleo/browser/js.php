<?
class js
//Clase para funciones javascript.
{
	function abrir()
	{
		return '<script language="JavaScript" type="text/javascript">';
	}
	//-------------------------------------------------------------------------------------
	function cerrar()
	{
		return '</script>';
	}
	//-------------------------------------------------------------------------------------
	function checks_intercalar()
	{
		$salida = 	'
			function checks_intercalar(check)
			//Recibe el check de control como parametro
			{
				if (check.checked)
					checks_marcar(check.form, check.name);
				else
					checks_desmarcar(check.form, check.name);
			}
		';
		return $salida;
	}
	//-------------------------------------------------------------------------------------

	function checks_marcar()
	{
		$salida = 	'
			function checks_marcar(f, excepcion)
			//Recibe el formulario como parametro
			{
				for(i=0;i<f.elements.length;i++)
					if(f.elements[i].type=="checkbox" && f.elements[i].name != excepcion) 
						f.elements[i].checked=true;
			}
		';
		return $salida;
	}

	//-------------------------------------------------------------------------------------
	
	function checks_desmarcar()
	{
		$salida = 	'
			function checks_desmarcar(f, excepcion)
			//Recibe el formulario como parametro
			{
				for(i=0;i<f.elements.length;i++)
					if(f.elements[i].type=="checkbox" && f.elements[i].name != excepcion) 
						f.elements[i].checked=false;
			}
		';
		return $salida;
	}
	//-------------------------------------------------------------------------------------
	
	function checks_alguno_marcado()
	{
		$salida = 	"
			function checks_alguno_marcado(f)
			//Recibe el formulario como parametro
			{
				uno_marcado = false;
				i = 0;
				while(i<f.elements.length && !uno_marcado) {
					if(f.elements[i].type=='checkbox')// && ){
						uno_marcado = (f.elements[i].checked == true);
					}		
					i++;
				}
				return (uno_marcado);
			}
		";
		return $salida;
	}
	//-------------------------------------------------------------------------------------
	

}
?>