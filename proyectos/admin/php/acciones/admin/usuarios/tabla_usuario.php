<?

echo ei_mensaje("Esta funcionalidad no esta terminada. El objetivo central de la 
misma es proveer un mecanismo de replicacion de usuarios en una fuente de datos
distinta a la de la instancia");

enter();
$crear_tabla = "CREATE TABLE apex_usuario
(  
   usuario                       varchar(20)    NOT NULL,
   nombre                        varchar(80)    NULL,
   CONSTRAINT  apex_usuario_pk    PRIMARY KEY (usuario)
);";
echo "<pre>$crear_tabla</pre>";
?>