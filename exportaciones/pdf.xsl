<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:output encoding="ISO-8859-1" method="xml"/>
	

	<xsl:template match="raiz">
		<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
			<fo:layout-master-set>
				<fo:simple-page-master master-name="report" margin-top="1cm" margin-bottom="1cm" margin-left="2cm" margin-right="2cm">
					<xsl:choose>
						<xsl:when test="child::*[position()=1][attribute::orientacion='landscape']">
							<xsl:attribute name="page-width">297mm</xsl:attribute>
						 	<xsl:attribute name="page-height">210mm</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="page-width">210mm</xsl:attribute>
						 	<xsl:attribute name="page-height">297mm</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<fo:region-body margin-top="2.5cm"
						margin-bottom="2cm" />
					<fo:region-before extent="2cm"/>
					<fo:region-after extent="2cm" />
				</fo:simple-page-master>


			</fo:layout-master-set>
			<fo:page-sequence master-reference="report" id="rps">
				<fo:static-content flow-name="xsl-region-after">
					<fo:block font-size="7pt" text-align="outside">
						<fo:inline>
							Pág. <fo:page-number/> de <fo:page-number-citation-last ref-id="rps"/>
						</fo:inline>
					</fo:block>
				</fo:static-content>
				<xsl:apply-templates />
			</fo:page-sequence>

		</fo:root>
	</xsl:template>

	<xsl:template match="ci | tabla">
		<xsl:choose>
			<xsl:when test="not(ancestor::ci)">
				<xsl:if test="@titulo">
					<xsl:call-template name="crear_cabecera"/>
				</xsl:if>
				<fo:flow flow-name="xsl-region-body">
					<xsl:choose>
						<xsl:when test="child::*">
							<xsl:apply-templates />
						</xsl:when>
						<xsl:otherwise>
							<fo:block/>
						</xsl:otherwise>
					</xsl:choose>
				</fo:flow>
			</xsl:when>
			<xsl:otherwise>
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
				<xsl:apply-templates />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="texto">
		<fo:block>
			<xsl:for-each select="att">
				<xsl:attribute name="{@nombre}">
					 <xsl:value-of select="@valor"/>
				</xsl:attribute>
			</xsl:for-each>
			<xsl:value-of select="@valor"/>
		</fo:block>
	</xsl:template>
	
	<xsl:template match="img">
		<xsl:choose>
			<xsl:when test="not(ancestor::ci)">
				<xsl:if test="@titulo">
					<xsl:call-template name="crear_cabecera"/>
				</xsl:if>
				<fo:flow flow-name="xsl-region-body">
					<fo:block font-size="8pt" margin-bottom=".7cm" text-align="center">
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
						<fo:block keep-with-previous.within-column="always">
							<xsl:value-of select="@caption"/>
						</fo:block>
					</xsl:if>
				</fo:flow>
			</xsl:when>
			<xsl:otherwise>
				<fo:block font-size="8pt"  margin-bottom=".7cm">
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
						<fo:block keep-with-previous.within-column="always">
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
		<fo:block font-size = '8pt' margin-bottom="0.4cm">
		<fo:table>
			<xsl:choose>
			<xsl:when test="not(fila) and dato">
				<fo:table-column column-width="30%"/>
				<fo:table-column/>
				<fo:table-body>
				<xsl:for-each select="dato">
						<fo:table-row>
							<fo:table-cell>
								<fo:block font-weight="bold"
									margin-right="1cm">
									<xsl:value-of select="@clave" />
									:
								</fo:block>
							</fo:table-cell>
							<fo:table-cell>
								<fo:block>
									<xsl:value-of select="@valor" />
								</fo:block>
							</fo:table-cell>
						</fo:table-row>
				</xsl:for-each>
				</fo:table-body>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="col[attribute::width]">
					<xsl:for-each select="col">
						<fo:table-column>
							<xsl:if test="@width">
								<xsl:attribute name="column-width">
									<xsl:value-of select="@width"/>
								</xsl:attribute>
							</xsl:if>
						</fo:table-column>
					</xsl:for-each>
				</xsl:if>
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
								<fo:table-cell border="medium solid black">
									<fo:block>
										<fo:block margin="0.1cm">
											<xsl:if test="../../col[position() = $pos][attribute::text-align]">
												<xsl:attribute name="text-align">
													<xsl:value-of select="../../col[position() = $pos]/@text-align"/>
												</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="@valor" />
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
		<fo:static-content flow-name="xsl-region-before">
			<fo:block border-after-width="thin" border-after-style="solid">
				<fo:table>
					<xsl:if test="@logo">
					<fo:table-column column-width="15%"/>
					<fo:table-column column-width="70%"/>
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
			</fo:block>
		</fo:static-content>
	</xsl:template>
</xsl:stylesheet>
