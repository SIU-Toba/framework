<?php

	function acceso_post()
	//Devuelde TRUE si la hoja se accedio por POST
	{
		return ($_SERVER["REQUEST_METHOD"]=="POST");
	}
	//-----------------------------------------------------------------

	function acceso_get()
	//Devuelve TRUE si el acceso se dio por GET
	{
		return ($_SERVER["REQUEST_METHOD"]=="GET");
	}
	//-----------------------------------------------------------------

?>