<?
//require_once("nucleo/consola/error.php");			//Genera Encabezados de HTTP


//		ATENCION, falta finalizar objetos


//Tipo de SOLICITUD
define("apex_solicitud_tipo","consola");	
//Pagina INICIAL
define("apex_pa_item_inicial","toba,/consola/menu");//Pagina inicial
//---- Registra la solicitud en la base
define("apex_pa_registrar_solicitud","db");// VALORES POSIBLES: nunca, siempre, db
//---- Guarda el benchmark de la generacion del item
define("apex_pa_registrar_cronometro","db");//VALORES POSIBLES: nunca, siempre, db

//---------------------------------------------------------------------------
//--[1]--  Recupero parametros OBLIGATORIOS  --------------------------------
//---------------------------------------------------------------------------
    
    //print_r($argv);exit();//PROBAR ARGUMENTOS
    $invocacion = " INVOCACION CORRECTA:   ". $argv[0] . " instancia usuario proyecto item [parametros]\n";
    //-- INSTANCIA --
	if(isset($argv[1])){
        define("apex_pa_instancia",$argv[1]);
	}else{
        echo " ERROR: Es necesario especificar un INSTANCIA\n";
        echo $invocacion;
        exit(3);
    }
    //-- USUARIO --
	if(isset($argv[2])){
        $usuario = $argv[2];
	}else{
        echo " ERROR: Es necesario especificar un USUARIO\n";
        echo $invocacion;
        exit(3);
    }
    //-- ITEM (proyecto/clave) --
	if((isset($argv[3]))&&(isset($argv[4])))
	{
        $proyecto = $argv[3];
        $item[0] = $proyecto;// Proyecto del ITEM
		$item[1] = $argv[4];// Clave del ITEM
	}else{  //ITEM por DEFECTO.
        echo " ERROR: Es necesario especificar un ITEM\n";
        echo $invocacion;
        exit(2);
    }
	//-- VALOR DE $TOBA_DIR --
	$dir_toba = $_ENV['toba_dir'];
	$dir_toba = "$dir_toba/php";
	$dir_toba = str_replace("\\", "/", $dir_toba);					//Cambia limitadores a formato unix
	
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
	ini_set("include_path", ini_get("include_path"). $separador . $dir_toba);

//---------------------------------------------------------------------------
//--[2]--  Genero la SOLICITUD  ---------------------------------------------
//---------------------------------------------------------------------------

    require_once("nucleo/acceso_inicio.inc.php");
	
	$solicitud =& new solicitud_consola($item,$usuario);
	if( $solicitud->tipo_solicitud() == "browser" )
	{
		//El item solicitado es de tipo BROWSER.
		//Emulo el ambiente WEB.
		//Seria interesante tener un ITEM serializador de sesiones y un 
		//mecanismo para levantar de esta forma una sesion especifica
		$_SERVER["REMOTE_ADDR"]="localhost";
		$_SERVER["REQUEST_METHOD"] = "GET";
		require_once("nucleo/consola/emular_web_pa.php");
		require_once("nucleo/consola/emular_web_inc.php");
		sesion::abrir($usuario, $proyecto);
		require_once("nucleo/browser/hilo.php");
		$solicitud->hilo =& new hilo();
	}
	$solicitud->procesar();

    require_once("nucleo/acceso_fin.inc.php");
	exit( $solicitud->obtener_estado_proceso() );
    
//---------------------------------------------------------------------------
//---------------------------------------------------------------------------
?>