<?php
	$proyecto = toba::proyecto()->get_parametro('descripcion');
	$ico = toba_recurso::imagen_proyecto('favicon.ico');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><?php echo $proyecto ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="icon" href="<?php echo $ico;?>" type="image/x-icon" /><link rel="shortcut icon" href="<?php echo $ico;?>" type="image/x-icon" />
</head>

<frameset rows="*" cols="380,*" frameborder="YES" border="1" bordercolor="#553DA1" framespacing="0" id='frameset_admin'>
  <frameset rows="59,*" frameborder="YES" border="1"  bordercolor="#553DA1" framespacing="0">
    <frame src="<?php echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),1000241)?>" name="<?php echo  apex_frame_control ?>" scrolling="NO">
    <frame src="<?php echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),1000239,null,false,false,null,true,'lateral')?>" name="<?php echo  apex_frame_lista ?>" scrolling="auto">
  </frameset>
  <frame src="<?php echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),1000265)?>" name="<?php echo  apex_frame_centro ?>" scrolling="auto">
</frameset>

</html>
