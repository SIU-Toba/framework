<?

	//Entrada a la ZONA clase OBJETO
	$clase_objeto =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto');
	$vinculo_objeto = $this->vinculador->obtener_vinculo_a_item('admin',
																"/admin/apex/clase_autodoc",
																$clase_objeto );

	$clase_hilo =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'hilo');
	$vinculo_hilo = $this->vinculador->obtener_vinculo_a_item('admin',
																"/admin/apex/nucleo_autodoc",
																$clase_hilo );

	$clase_solicitud =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'solicitud');
	$vinculo_solicitud = $this->vinculador->obtener_vinculo_a_item('admin',
																"/admin/apex/nucleo_autodoc",
																$clase_solicitud );

	$clase_vinculador =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'vinculador');
	$vinculo_vinculador = $this->vinculador->obtener_vinculo_a_item('admin',
																"/admin/apex/nucleo_autodoc",
																$clase_vinculador );
?>
<map name="mapa" id="mapa">
  <area shape="rect" coords="118,2,308,76" href="<? echo $vinculo_hilo ?>" alt="hilo">
  <area shape="rect" coords="369,2,557,75" href="<? echo $vinculo_vinculador ?>" alt="vinculador">
  <area shape="rect" coords="228,134,417,209" href="<? echo $vinculo_solicitud ?>" alt="solicitud">
  <area shape="rect" coords="245,384,398,441" href="<? echo $vinculo_objeto ?>" alt="objeto">
  <area shape="rect" coords="473,510,624,569" href="<? echo $vinculo_objeto ?>" alt="objeto">
</map>
</body>
<?php
    echo ei_centrar(recurso::imagen_apl('diapositivas/ii.png',true,null,null,null,"#mapa"));
?>