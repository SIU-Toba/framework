<?

//REformar para transformar en un hilo emulado (que levante la sesion, el get y el post de un archivo)

//CLAVES del querystring
define("apex_hilo_qs_id","ah");
define("apex_hilo_qs_item","ai");	
define("apex_hilo_qs_zona","az");	

class hilo_emulado
//#@desc: Mantiene el ESTADO.
{
	var $id;
	var $url_actual;
	var $item_solicitado;
	var $zona;
	
	function hilo_emulado()
	//#@desc: Crea una instancia del hilo
	{
		$this->url_actual = "consola://x.php";
		$this->id = uniqid("");
		if(isset($_GET[apex_hilo_qs_item])){
			$this->item_solicitado=$_GET[apex_hilo_qs_item];
		}
		if(isset($_GET[apex_hilo_qs_zona])){
			$this->zona=$_GET[apex_hilo_qs_zona];
		}
	}

//---------------- INTERFACE con la SOLICITUD -----------------

	function obtener_id(){
		return $this->id;
	}

	function obtener_item_solicitado()
	{
		return $this->item_solicitado;
	}

//------------------------ VINCULO -----------------------------

	function auto_vinculo()
	//Genera la primera porcion de las URLs
	{
		return $this->url_actual . "?" . apex_hilo_qs_id  . "=" . $this->id;
	}
}
?>