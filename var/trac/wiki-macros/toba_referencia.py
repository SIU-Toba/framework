"""
    Hace un link al api de Toba generado por phpDoc [[BR]]
    Sintaxis: {{{[[toba_referencia(item, descripcion)]]}}} [[BR]]
"""

def execute(hdf, txt, env):
    args = txt.split(',', 2)
    url = "/toba_referencia_trunk/aplicacion.php?ai=toba_referencia||%s" % (args[0].strip())
    descripcion = args[1].strip()
    #descripcion = "<img style='vertical-align:middle' src='/toba_editor_trunk/doc/api/media/php-small.png' />" + descripcion
    salida = "<a href='%s' title='Navega hacia el ejemplo en el proyecto online de referencia'>%s</a>" % (url, descripcion)
    return salida
    