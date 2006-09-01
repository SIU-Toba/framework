<?
//------------------------------------------------------------------------
class fuente_toba
{
	protected $datos_db;
	protected $id;
	protected $proyecto;
	
	function __construct($id, $proyecto=null)
	{

		global $db, $ADODB_FETCH_MODE, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		if(isset($proyecto)){
			$proyecto_fuente = $proyecto;
		}else{
			$proyecto_fuente = $solicitud->hilo->obtener_proyecto();
		}
		
		$sql = "SELECT 	*
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = '$id'
				AND		proyecto = '$proyecto_fuente';";

		$rs = $db["instancia"][apex_db_con]->Execute($sql);

		if ((!$rs) || ($rs->EOF)) {
			throw new toba_error("FUENTE_TOBA: no existe la fuente de Datos '{$id}' para el proyecto '{$proyecto_fuente}'");
		} else {
			//La conexion es un LINK a la conexion primaria de la INSTANCIA?
			if (isset($rs->fields["link_instancia"]) && ($rs->fields["link_instancia"] == 1)) {
				//La fuente solicita un LINK a un elemento del archivo de INSTANCIAS
				if (isset($rs->fields["instancia_id"]) && (trim($rs->fields["instancia_id"])!="")) {
					global $instancia;
					$instancia_id = $rs->fields["instancia_id"];
					//Existe una descripcion de esa instancia?
					if (isset($instancia[$instancia_id])) {
						// Seteo los valores de datos_db con la def. de instancia del link
						$this->datos_db[apex_db_motor] = $instancia[$instancia_id][apex_db_motor];
						$this->datos_db[apex_db_profile] = $instancia[$instancia_id][apex_db_profile];
						$this->datos_db[apex_db_usuario] = $instancia[$instancia_id][apex_db_usuario];
						$this->datos_db[apex_db_clave] = $instancia[$instancia_id][apex_db_clave];
						$this->datos_db[apex_db_base] = $instancia[$instancia_id][apex_db_base];
					} else {
						throw new toba_error("FUENTE_TOBA: no existe el indice en 'instancias.php'");
					}
				}
			} else {
				$this->datos_db[apex_db_motor] = $rs->fields["fuente_datos_motor"];
				$this->datos_db[apex_db_profile] = $rs->fields["host"];
				$this->datos_db[apex_db_usuario] = $rs->fields["usuario"];
				$this->datos_db[apex_db_clave] = $rs->fields["clave"];
				$this->datos_db[apex_db_base] = $rs->fields["base"];
			}
		}

	}

	
	function conectar()
	//Esta función conecta a la BD dada en $this->datos_db[apex_db_base]
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		if( $db[$this->id][apex_db_con] =& ADONewConnection($this->datos_db[apex_db_motor]) ){
			$ok = $db[$this->id][apex_db_con]->Connect($this->datos_db[apex_db_profile],$this->datos_db[apex_db_usuario],$this->datos_db[apex_db_clave],$this->datos_db[apex_db_base]);
			if( $ok ){
				//Dejo guardados los parametros de conexion
				$db[$this->id][apex_db_motor] = $this->datos_db[apex_db_motor];
				$db[$this->id][apex_db_profile] = $this->datos_db[apex_db_profile];
				$db[$this->id][apex_db_usuario] = $this->datos_db[apex_db_usuario];
				$db[$this->id][apex_db_clave] = $this->datos_db[apex_db_clave];
				$db[$this->id][apex_db_base] = $this->datos_db[apex_db_base];

				$clase_fuente = "fuente_datos_".$db[$this->id][apex_db_motor];
				$db[$this->id][apex_db] = new $clase_fuente($db[$this->id][apex_db_con]);

			}else{	//Se creo la conexion?
				return false;
			}
		}else{		//Se creo la conexion?
			return false;
		}
	}


	function ejecutar_archivo($sql)
	//Esta función ejecuta una serie de comandos sql dados en un archivo, contra la BD dada.
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		
		$sql = "/".$sql;
		$str = file_get_contents(dirname(__FILE__).$sql);

		if ($db[$this->id][apex_db_con]->Execute($str) == false) {
			throw new toba_error("FUENTE_TOBA: Imposible ejecutar comando '{$str}' : ".$db[$this->id][apex_db_con]->ErrorMsg());
		}

	}


	function existe_db()
	//Esta función chequea si existe la BD a la cual se debería estar conectado
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		if( $cconn =& ADONewConnection($this->datos_db[apex_db_motor]) ){
			// Se incluye un "@" adelante del llamado a la función para que no devuelva WARNINGS,
			// ya que si la BD no existe, esta función envía un WARNING avisando la situación.
			$ok = @$cconn->Connect($this->datos_db[apex_db_profile],$this->datos_db[apex_db_usuario],$this->datos_db[apex_db_clave],$this->datos_db[apex_db_base]);
			if( $ok ){
				$cconn->Close();
				return true;
			}else{
				return false;
			}
		}else{
			throw new toba_error("FUENTE_TOBA: Imposible conectarse al Motor de BD predeterminado");
		}
	}
	

	function crear_db()
	//Esta función crea la BD dada en $this->datos_db[apex_db_base]
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		$sql = " CREATE DATABASE {$this->datos_db[apex_db_base]}";
		
		if ($db["instancia"][apex_db_con]->Execute($sql) == false) {
			throw new toba_error("FUENTE_TOBA: Imposible crear Base de Datos : ".$conn->ErrorMsg());
		}

	}	

}

//------------------------------------------------------------------------
?>