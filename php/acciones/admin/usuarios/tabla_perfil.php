<?
echo ei_mensaje("Esta funcionalidad no esta terminada. El objetivo central de la 
misma es proveer un mecanismo de replicacion de perfiles de datos en una fuente de datos
distinta a la de la instancia");

$sql = "CREATE TABLE apex_usuario_perfil_datos
(  
   proyecto                   varchar(15)    NOT NULL,
   usuario_perfil_datos       varchar(20)    NOT NULL,
   nombre                     varchar(80)    NOT NULL,
   descripcion                varchar(255)	NULL,
   PRIMARY   KEY (proyecto, usuario_perfil_datos)
);\n\n";

	$perfiles = recuperar_datos("SELECT proyecto, 
											usuario_perfil_datos,
											nombre,
											descripcion
											FROM apex_usuario_perfil_datos 
											WHERE proyecto = '".$this->hilo->obtener_proyecto()."'
											AND usuario_perfil_datos <> 'no';");
	
	include_once("nucleo/lib/sql.php");
	foreach($perfiles[1] as $perfil){
		$sql .= sql_array_a_insert("apex_usuario_perfil_datos",$perfil);		
	}


	$form = "no";
	include_once("nucleo/browser/interface/ef.php");
	echo "<div align='center'>";
	echo form::abrir($form, $this->vinculador->generar_solicitud(null,null,null,true));
	$input_sql =& new ef_editable_multilinea($form,$form,"sql","SQL","","sql",null,array("resaltar"=>1,"filas"=>5,"columnas"=>60,""));
	$input_sql->cargar_estado($sql);
	echo $input_sql->obtener_input();
	echo form::cerrar();
	echo "</div>";
enter();

echo "<pre>";
echo $sql;
echo "</pre>";
?>