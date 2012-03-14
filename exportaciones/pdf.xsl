<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:output encoding="ISO-8859-1" method="xml"/>
	

	<xsl:template match="raiz">
		<xsl:variable name="w">
			<xsl:choose>
				<xsl:when test="child::*[position()=1][attribute::ancho]">
					<xsl:value-of select="child::*[position()=1]/attribute::ancho"/>
				</xsl:when>
				<xsl:otherwise>210mm</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="h">
			<xsl:choose>
				<xsl:when test="child::*[position()=1][attribute::alto]">
					<xsl:value-of select="child::*[position()=1]/attribute::alto"/>
				</xsl:when>
				<xsl:otherwise>297mm</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="t">
			<xsl:choose>
				<xsl:when test="child::*[position()=1][attribute::margen_sup]">
					<xsl:value-of select="child::*[position()=1]/attribute::margen_sup"/>
				</xsl:when>
				<xsl:otherwise>1cm</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="b">
			<xsl:choose>
				<xsl:when test="child::*[position()=1][attribute::margen_inf]">
					<xsl:value-of select="child::*[position()=1]/attribute::margen_inf"/>
				</xsl:when>
				<xsl:otherwise>1cm</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="l">
			<xsl:choose>
				<xsl:when test="child::*[position()=1][attribute::margen_izq]">
					<xsl:value-of select="child::*[position()=1]/attribute::margen_izq"/>
				</xsl:when>
				<xsl:otherwise>2cm</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="r">
			<xsl:choose>
				<xsl:when test="child::*[position()=1][attribute::margen_der]">
					<xsl:value-of select="child::*[position()=1]/attribute::margen_der"/>
				</xsl:when>
				<xsl:otherwise>2cm</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
			<fo:layout-master-set>
				<fo:simple-page-master master-name="report" margin-top="{$t}" margin-bottom="{$b}" margin-left="{$l}" margin-right="{$r}">
					<xsl:choose>
						<xsl:when test="child::*[position()=1][attribute::orientacion='landscape']">
							<xsl:attribute name="page-width"><xsl:value-of select="$h"/></xsl:attribute>
						 	<xsl:attribute name="page-height"><xsl:value-of select="$w"/></xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="page-width"><xsl:value-of select="$w"/></xsl:attribute>
						 	<xsl:attribute name="page-height"><xsl:value-of select="$h"/></xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<fo:region-body>
						<xsl:attribute name="margin-top">
							<xsl:choose>
								<xsl:when test="child::*[position()=1]/@cabecera = 'false'">0cm</xsl:when>
								<xsl:when test="child::*[position()=1]/@cab_size"><xsl:value-of select="child::*[position()=1]/@cab_size"/></xsl:when>
								<xsl:otherwise>3.5cm</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>	
						<xsl:attribute name="margin-bottom">
							<xsl:choose>
								<xsl:when test="child::*[position()=1]/@pie = 'false'">0cm</xsl:when>
								<xsl:when test="child::*[position()=1]/@pie_size"><xsl:value-of select="child::*[position()=1]/@pie_size"/></xsl:when>
								<xsl:otherwise>2cm</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
					</fo:region-body>
					<fo:region-before>
						<xsl:attribute name="extent">
							<xsl:choose>
								<xsl:when test="child::*[position()=1]/@cab_size"><xsl:value-of select="child::*[position()=1]/@cab_size"/></xsl:when>
								<xsl:otherwise>2cm</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
					</fo:region-before>
					<fo:region-after>
						<xsl:attribute name="extent">
							<xsl:choose>
								<xsl:when test="child::*[position()=1]/@pie_size"><xsl:value-of select="child::*[position()=1]/@pie_size"/></xsl:when>
								<xsl:otherwise>2cm</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>					
					</fo:region-after>
				</fo:simple-page-master>
			</fo:layout-master-set>
	
			<xsl:call-template name="page-seq">
				<xsl:with-param name="loop">1</xsl:with-param>
			</xsl:call-template>

		</fo:root>
	</xsl:template>

	<xsl:template name="page-seq">
	<xsl:param name="loop"/>
				<fo:page-sequence master-reference="report" force-page-count="no-force" initial-page-number="1">
				<xsl:attribute name="id">rps<xsl:value-of select="$loop"/></xsl:attribute>
				<xsl:if test="not(child::*[position()=1]/@pie) or child::*[position()=1]/@pie != 'false'">
				<fo:static-content flow-name="xsl-region-after">
				<fo:block border-before-width="thin" border-before-style="solid">
					<xsl:choose>
						<xsl:when test="child::*[position()=1]/pie">
							<xsl:apply-templates select="child::*[position()=1]/pie/*">
								<xsl:with-param name="loop" select="$loop"/>
							</xsl:apply-templates>
						</xsl:when>
						<xsl:otherwise>
							<fo:block font-size="7pt" text-align="outside">
								<fo:inline>
									Pág. <fo:page-number/> de <fo:page-number-citation-last><xsl:attribute name="ref-id">rps<xsl:value-of select="$loop"/></xsl:attribute></fo:page-number-citation-last>
								</fo:inline>
							</fo:block>
						</xsl:otherwise>
					</xsl:choose>
				</fo:block>
				</fo:static-content>
				</xsl:if>
				<xsl:apply-templates>
					<xsl:with-param name="loop" select="$loop"/>
				</xsl:apply-templates>
			</fo:page-sequence>
			<xsl:if test="child::*[position()=1]/@copia and $loop &lt; child::*[position()=1]/@copia">
				<xsl:call-template name="page-seq">
					<xsl:with-param name="loop">
						<xsl:value-of select="$loop +1"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:if>
	</xsl:template>

	<xsl:template match="pagina-actual">
	<fo:page-number/>
	</xsl:template>

	<xsl:template match="pagina-total">
	<xsl:param name="loop"/>
	<fo:page-number-citation-last><xsl:attribute name="ref-id">rps<xsl:value-of select="$loop"/></xsl:attribute></fo:page-number-citation-last>
	</xsl:template>

	<xsl:template match="ci | tabla">
	<xsl:param name="loop"/>
		<xsl:choose>
			<xsl:when test="not(ancestor::ci) and not(ancestor::tabla)">
				<xsl:if test="(@titulo or cabecera) and (not(@cabecera) or @cabecera != 'false')">
					<xsl:call-template name="crear_cabecera">
						<xsl:with-param name="loop" select="$loop"/>
					</xsl:call-template>
				</xsl:if>
				<fo:flow flow-name="xsl-region-body">
					<fo:block>
						<xsl:for-each select="./@*[not(name(.) = 'titulo' or name(.) = 'logo' or name(.) = 'subtitulo' or name(.) = 'orientacion' or name(.) = 'alto' or name(.) = 'ancho' or name(.) = 'copia' or starts-with(name(.),'pie') or starts-with(name(.),'cab') or starts-with(name(.), 'margen'))]">
							<xsl:copy-of select="."/>
						</xsl:for-each>
						<xsl:choose>
							<xsl:when test="child::*">
								<xsl:apply-templates select="*[not(local-name(.) = 'pie') and not(local-name(.) = 'cabecera')]"/>
							</xsl:when>
							<xsl:otherwise>
								<fo:block/>
							</xsl:otherwise>
						</xsl:choose>
					</fo:block>
				</fo:flow>
			</xsl:when>
			<xsl:otherwise>
				<fo:block>
					<xsl:for-each select="./@*[not(name(.) = 'titulo' or name(.) = 'logo' or name(.) = 'subtitulo' or name(.) = 'orientacion' or name(.) = 'alto' or name(.) = 'ancho' or name(.) = 'copia' or starts-with(name(.),'pie') or starts-with(name(.),'cab') or starts-with(name(.), 'margen'))]">
						<xsl:copy-of select="."/>
					</xsl:for-each>
					<xsl:if test="@titulo">
						<xsl:choose>
						<xsl:when test="name(.) = 'ci'">
							<fo:block font-size="12pt" font-weight="bold"
								text-align="center" space-after=".5cm">
								<xsl:value-of select="@titulo" />
								<xsl:if test="@subtitulo">
									<fo:block font-size="10pt" text-align="center">
										<xsl:value-of select="@subtitulo" />
									</fo:block>
								</xsl:if>
							</fo:block>
						</xsl:when>
						<xsl:otherwise>
							<fo:block font-size="9pt" font-weight="bold" margin-bottom=".2cm" text-decoration="underline" keep-with-next="always">
								<xsl:value-of select="@titulo" />
							</fo:block>
						</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					<xsl:apply-templates select="*[not(local-name(.) = 'pie') and not(local-name(.) = 'cabecera')]">
						<xsl:with-param name="loop" select="$loop"/>
					</xsl:apply-templates>
				</fo:block>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="texto">
	<xsl:param name="loop"/>
		<fo:block>
			<xsl:for-each select="./@*">
					 <xsl:copy-of select="."/>
			</xsl:for-each>
			<xsl:apply-templates>
				<xsl:with-param name="loop" select="$loop"/>
			</xsl:apply-templates>
		</fo:block>
	</xsl:template>
	
	<xsl:template match="img">
	<xsl:param name="loop"/>
		<xsl:choose>
			<xsl:when test="not(ancestor::ci)">

				<xsl:if test="@titulo  and (not(@cabecera) or @cabecera != 'false')">
					<xsl:call-template name="crear_cabecera">
						<xsl:with-param name="loop" select="$loop"/>
					</xsl:call-template>
				</xsl:if>
				<fo:flow flow-name="xsl-region-body">
					<fo:block font-size="8pt" text-align="center">
						<xsl:choose>
						<xsl:when test="@type='svg'">
							<fo:instream-foreign-object>
								<xsl:copy-of select="child::*[position() = 1]"/>
							</fo:instream-foreign-object>
						</xsl:when>
						<xsl:otherwise>
						<fo:external-graphic  block-progression-dimension="100%" inline-progression-dimension="100%" content-width="scale-down-to-fit" content-height="scale-down-to-fit" src="{@src}" />
						</xsl:otherwise>
						</xsl:choose>
					</fo:block>
					<xsl:if test="@caption">
						<fo:block margin-top=".7cm" keep-with-previous.within-column="always">
							<xsl:value-of select="@caption"/>
						</fo:block>
					</xsl:if>
				</fo:flow>
			</xsl:when>
			<xsl:otherwise>
				<fo:block font-size="8pt">
					<xsl:if test="@titulo">
						<fo:block font-size="9pt" font-weight="bold" margin-bottom=".2cm" text-decoration="underline" keep-with-next="always">
							<xsl:value-of select="@titulo" />
						</fo:block>
					</xsl:if>
					<fo:block  text-align="center">
					<xsl:choose>
					<xsl:when test="@type='svg'">
						<fo:instream-foreign-object block-progression-dimension="100%" inline-progression-dimension="100%" content-width="scale-down-to-fit" content-height="scale-down-to-fit">
							<xsl:copy-of select="child::*[position() = 1]"/>
						</fo:instream-foreign-object>
					</xsl:when>
					<xsl:otherwise>
					<fo:external-graphic  block-progression-dimension="100%" inline-progression-dimension="100%" content-width="scale-down-to-fit" content-height="scale-down-to-fit" src="{@src}" />
					</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="@caption">
						<fo:block margin-top=".7cm" keep-with-previous.within-column="always">
							<xsl:value-of select="@caption"/>
						</fo:block>
					</xsl:if>
					</fo:block>
				</fo:block>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="cc">
		<fo:block background-color="#cceeff" border="medium solid black" font-size='8.5pt'><fo:inline padding="0.2cm"><xsl:value-of select="."/></fo:inline></fo:block>
	</xsl:template>
	
	<xsl:template match="datos">
	<xsl:param name="loop"/>
		<fo:block font-size = '8pt' margin-bottom="0.4cm">
		<fo:table>
			<xsl:choose>
			<xsl:when test="not(fila) and dato">
				<fo:table-column column-width="4cm"/>
				<fo:table-column/>
				<fo:table-body>
				<xsl:for-each select="dato">
						<fo:table-row>
							<fo:table-cell>
								<fo:block font-weight="bold"
									margin-right="1cm">
									<xsl:value-of select="@clave" />
									<xsl:choose>
										<xsl:when test="@clave!=''">
									        :
									    </xsl:when>
								    </xsl:choose>
								</fo:block>
							</fo:table-cell>
							<fo:table-cell>
								<fo:block>
									<xsl:choose>
										<xsl:when test="@valor">
											<xsl:value-of select="@valor" />
										</xsl:when>
										<xsl:otherwise>
											<xsl:apply-templates>
												<xsl:with-param name="loop" select="$loop"/>
											</xsl:apply-templates>
										</xsl:otherwise>
									</xsl:choose>
								</fo:block>
							</fo:table-cell>
						</fo:table-row>
				</xsl:for-each>
				</fo:table-body>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="col">
					<fo:table-column>
						<xsl:for-each select="./@*[name(.)!='titulo']">
							<xsl:choose>
							<xsl:when test="name(.) = 'width' and not(../@column-width)">
								<xsl:attribute name="column-width">
									<xsl:value-of select="."/>
								</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="{name(.)}">
									<xsl:value-of select="."/>
								</xsl:attribute>
							</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</fo:table-column>
				</xsl:for-each>
				<xsl:if test="col[attribute::titulo]">
					<fo:table-header>
					<xsl:for-each select="col">
						<fo:table-cell  border="medium solid black" background-color='#dddddd'>
							<fo:block text-align="center" font-weight="bold"><fo:block margin="0.1cm"><xsl:value-of select="@titulo"/></fo:block></fo:block>
						</fo:table-cell>
					</xsl:for-each>
					</fo:table-header>
				</xsl:if>
				<fo:table-body>
				<xsl:for-each select="fila">
						<fo:table-row>
							<xsl:for-each select="dato">
								<xsl:variable name="pos" select="position()"/>
								<fo:table-cell>
									<xsl:if test="not(@*[starts-with(name(.), 'border')])">
										<xsl:attribute name="border">medium solid black</xsl:attribute>
									</xsl:if>
									<xsl:for-each select="./@*[not(name(.) = 'valor' or name(.) = 'text-align' or name(.) = 'width' or name(.) = 'clave')]">
										<xsl:variable name="nom" select="name(.)"/>
										<xsl:attribute name="{$nom}">
											<xsl:value-of select="."/>
										</xsl:attribute>
									</xsl:for-each>
									<fo:block>
										<fo:block margin="0.1cm">
											<xsl:choose>
											<xsl:when test="@text-align">
												<xsl:attribute name="text-align">
													<xsl:value-of select="@text-align"/>
												</xsl:attribute>
											</xsl:when>
											<xsl:when test="../../col[position() = $pos][attribute::text-align]">
												<xsl:attribute name="text-align">
													<xsl:value-of select="../../col[position() = $pos]/@text-align"/>
												</xsl:attribute>
											</xsl:when>
											</xsl:choose>
											<xsl:choose>
												<xsl:when test="@valor">
													<xsl:value-of select="@valor" />
												</xsl:when>
												<xsl:otherwise>
													<xsl:apply-templates>
														<xsl:with-param name="loop" select="$loop"/>
													</xsl:apply-templates>
												</xsl:otherwise>
											</xsl:choose>
										</fo:block>
									</fo:block>
								</fo:table-cell>
							</xsl:for-each>
						</fo:table-row>
				</xsl:for-each>
				</fo:table-body>
			</xsl:otherwise>
			</xsl:choose>
		</fo:table>
		</fo:block>
	</xsl:template>
	
	<xsl:template name="crear_cabecera">
	<xsl:param name="loop"/>
		<fo:static-content flow-name="xsl-region-before">
			<fo:block border-after-width="thin" border-after-style="solid">
				<xsl:choose>
					<xsl:when test="cabecera">
						<xsl:apply-templates select="cabecera/*">
							<xsl:with-param name="loop" select="$loop"/>
						</xsl:apply-templates>
					</xsl:when>
					<xsl:otherwise>
						<fo:table width="100%">
							<xsl:if test="@logo">
							<fo:table-column column-width="2cm"/>
							<fo:table-column/>
							</xsl:if>
							<fo:table-body>
								<fo:table-row>
									<xsl:if test="@logo">
									<fo:table-cell>
										<fo:block font-weight="bold">
											<fo:external-graphic src="{@logo}" content-width="scale-down-to-fit" content-height="scale-down-to-fit" width="100%"/>
										</fo:block>
									</fo:table-cell>
									</xsl:if>
									<fo:table-cell>
										<fo:block font-size="12pt" font-weight="bold" text-align="center">
											<xsl:value-of select="@titulo" />
											<xsl:if test="@subtitulo">
												<fo:block font-size="10pt" margin='0cm' padding='0cm'>
													<xsl:value-of select="@subtitulo" />
												</fo:block>
											</xsl:if>
										</fo:block>
									</fo:table-cell>
								</fo:table-row>
							</fo:table-body>
						</fo:table>
					</xsl:otherwise>
				</xsl:choose>
			</fo:block>
		</fo:static-content>
	</xsl:template>
</xsl:stylesheet>
