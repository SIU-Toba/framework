

function indentacion()
{
	for (i=0; i<indent ; i++)
	{
		printf("    ")
	}
}

function abrir_elemento(elemento)
{
	indentacion()
	printf("<%s>\n",elemento)
}

function cerrar_elemento(elemento)
{
	indentacion()
	printf("</%s>\n",elemento)
}

function abrir_elemento_a(elemento, atributos)
{
	indentacion()
	printf("<%s %s>\n",elemento, atributos)
}

function abrir_elemento_an(elemento, nombre)
{
	abrir_elemento_a(elemento, "nombre=\"" nombre "\"")
}

function elemento_simple(elemento, contenido)
{
	indentacion()
	printf("<%s>%s</%s>\n",elemento,contenido,elemento)
}

function elemento_simple_n(elemento, contenido)
{
	abrir_elemento(elemento)
	indentacion()
	printf("%s\n",contenido)
	cerrar_elemento(elemento)
}

###########################################################

BEGIN {
	schema = "I:\\apl\\doc\\def_xml\\schemas\\codigo.xsd"
	javascript=0
	indent=1
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
	print "<unidad_de_codigo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"" schema "\">"
}

END {
	print	"</unidad_de_codigo>"
}  

#--------------- Escapar JAVASCRIPT!!!

/<script/ {
	javascript=1
}

/<\/script/ {
	javascript=0
}

#---------------- Reconocer CLASES

($1 ~ /class/) && (javascript==0) {
	clase=$2
	atrib=""
	atrib=atrib "nombre=\"" $2 "\""
	if ($3=="extends"){
		atrib= atrib " ancestro=\"" $4 "\" "
	}
	abrir_elemento_a("clase",atrib)
	indent++
}

(/^}/) && (javascript==0) && !(clase=="") {
	indent--
	clase=""	
	cerrar_elemento("clase")
}

#---------------- Propiedades

($1 ~ /var/) && (javascript==0) {
	abrir_elemento_an("propiedad",$2) cerrar_elemento("propiedad")
}

#----------------- Reconocer Funciones 

($1 ~ /function/) && (javascript==0) {
	funcion=$2;
	atrib=""
	atrib=atrib "nombre=\"" $2 "\" tipo=\""
	if($2 == clase){
		atrib=atrib "constructor\""
	}else{
		atrib=atrib "normal\""
	}
	abrir_elemento_a("funcion",atrib)
	indent++
}

(/^	}/) && (javascript==0) && !(funcion=="") {
	indent--
	cerrar_elemento("funcion")
	funcion=""	
}

#----------------- Elementos genericos, acomodados por la indentacion

/#@desc/ {
	elemento_simple_n("descripcion", substr($0, 6))
}  
   
