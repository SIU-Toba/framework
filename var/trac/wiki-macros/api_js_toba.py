"""
    Hace un link al api JS de Toba generado por phpDoc [[BR]]
    Sintaxis: {{{[[api_js_toba(package/subpackage/clase, seccion_en_clase, texto]]}}} [[BR]]
    Ej:
    {{{
	[[api_js_toba(Objetos/Persistencia/objeto_datos_relacion, methoddump_esquema, Ver API)]]
    }}}
    [[api_js_toba(Objetos/Persistencia/objeto_datos_relacion, methoddump_esquema, Ver API)]]
"""

def execute(hdf, txt, env):
    args = txt.split(',', 2)
    clase = args[0].strip()
    seccion = len(args) > 1 and args[1].strip() or ""
    descripcion = len(args) > 2 and args[2].strip() or clase
    url = "/toba_editor_trunk/doc/api_js/%s.html" % (clase)
    descripcion = "<img style='vertical-align:middle' src='/toba_editor_trunk/doc/api/media/javascript-small.png' /> " + descripcion
    salida = "<a href='%s#%s' title='Navega hacia la documentacion JAVASCRIPT'>%s</a>" % (url, seccion, descripcion)
    return salida
    