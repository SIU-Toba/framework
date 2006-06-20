<?
	//Entrada a la ZONA clase OBJETO
	$clase_objeto =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto');
	$vinculo_objeto = $this->vinculador->obtener_vinculo_a_item("toba",
																"/admin/apex/clase_autodoc",
																$clase_objeto );

	$clase_cuadro =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_cuadro');
	$vinculo_cuadro = $this->vinculador->obtener_vinculo_a_item("toba",
																"/admin/apex/clase_autodoc",
																$clase_cuadro );

	$clase_solicitud =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'solicitud');
	$vinculo_solicitud = $this->vinculador->obtener_vinculo_a_item("toba",
																"/admin/apex/nucleo_autodoc",
																$clase_solicitud );
															
	$clase_ut =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_ut_formulario');
	$vinculo_ut = $this->vinculador->obtener_vinculo_a_item("toba",
																"/admin/apex/clase_autodoc",
																$clase_ut );
																
	$clase_form =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'form');
	$vinculo_form = $this->vinculador->obtener_vinculo_a_item("toba",
																"/admin/apex/nucleo_autodoc",
																$clase_form );	
																																																																	
	$clase_hoja =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_hoja');
	$vinculo_hoja = $this->vinculador->obtener_vinculo_a_item("toba",
																"/admin/apex/clase_autodoc",
																$clase_hoja );
?>
<map name="objetos"> 
  <area shape="rect" coords="-6,127,163,233" href="<? echo $vinculo_solicitud; ?>" alt="Solicitud" title="Solicitud"> 
  <area shape="rect" coords="259,117,464,247" href="<? echo $vinculo_objeto; ?>" alt="Objeto" title="Objeto"> 
  <area shape="rect" coords="265,275,446,364" href="#" alt="Objeto Filtro" title="Objeto Filtro">
  <area shape="rect" coords="553,238,727,314" href="<? echo $vinculo_ut; ?>" alt="Objeto UT Formulario" title="Objeto UT Formulario">
  <area shape="rect" coords="261,438,448,558" href="#" alt="Dimensi&oacute;n" title="Dimensi&oacute;n">
  <area shape="rect" coords="540,517,735,577" href="<? echo $vinculo_form; ?>" alt="Formulario" title="Formulario"> 
  <area shape="rect" coords="495,-2,662,65" href="#" alt="Objeto Hoja Contenido" title="Objeto Hoja Contenido"> 
  <area shape="rect" coords="267,-13,436,64" href="<? echo $vinculo_hoja; ?>" alt="Objeto Hoja" title="Objeto Hoja"> 
  <area shape="rect" coords="553,70,720,154" href="#" alt="Objeto Grafico" title="Objeto Grafico"> 
  <area shape="rect" coords="553,154,721,238" href="<? echo $vinculo_cuadro; ?>" alt="Objeto Cuadro" title="Objeto Cuadro"> 
  <area shape="rect" coords="540,375,730,506" href="#" alt="Elementos de Formulario (EF)" title="Elementos de Formulario (EF)"> 
  <area shape="rect" coords="273,696,470,748" href="#" alt="Form" title="Form"> 
</map> 
<?php
    echo ei_centrar(recurso::imagen_apl('diapositivas/objetos.png',true,null,null,null,"#objetos"));
?>