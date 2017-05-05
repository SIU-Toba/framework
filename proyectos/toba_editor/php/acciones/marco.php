<?php
	$proyecto = toba::proyecto()->get_parametro('descripcion');
	$ico = toba_recurso::imagen_proyecto('favicon.ico');
	$escapador = toba::escaper();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><?php echo $escapador->escapeHtml($proyecto); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="icon" href="<?php echo $ico; ?>" type="image/x-icon" /><link rel="shortcut icon" href="<?php echo $escapador->escapeHtmlAttr($ico); ?>" type="image/x-icon" />
</head>

<frameset rows="*" cols="410,*" frameborder="yes" border="2" bordercolor=white framespacing="0" id='frameset_admin'>
  <frameset rows="106,*" frameborder="NO" border="0"  framespacing="0">
    <frame src="<?php echo $escapador->escapeHtmlAttr(toba::vinculador()->get_url(toba_editor::get_id(), 1000241)); ?>" name="<?php echo  apex_frame_control; ?>" scrolling="NO">
    <frame src="<?php echo $escapador->escapeHtmlAttr(toba::vinculador()->get_url(toba_editor::get_id(), 1000239, array(), array('menu' => true, 'celda_memoria' => 'lateral'))); ?>" name="<?php echo  apex_frame_lista; ?>" scrolling="auto">
  </frameset>
  <frame src="<?php echo $escapador->escapeHtmlAttr(toba::vinculador()->get_url(toba_editor::get_id(), 1000265)); ?>" name="<?php echo  apex_frame_centro; ?>" scrolling="auto">
</frameset>

</html>
