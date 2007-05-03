"""
    Hace un link a una imagen de toba[[BR]]
    Sintaxis: {{{[[img_toba(path,proyecto)]]}}} [[BR]]
    Proyecto es opcional (por defecto usa el nucleo)
"""

def execute(hdf, txt, env):
    args = txt.split(',', 2)
    url = args[0].strip()   
    if len(args) > 1:
	url = "/%s_trunk/img/%s" % (args[1].strip(), url)
    else:
        url = "/toba_trunk/img/%s" % (url)

    salida = "<img onerror='alert(\"No se encuentra la imagen: \" + this.src)' style='vertical-align:middle' border=0 src='%s' />" % (url)
    return salida
    