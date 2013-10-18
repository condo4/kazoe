<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" />

	<xsl:variable name="query" select="php:functionString('GetKEnv','QUERY')" /> 
	
	<xsl:template match="/data">
		<data>
			<navi><xsl:attribute name="page"><xsl:value-of select="php:functionString('GetKEnv','PAGE')"/></xsl:attribute><xsl:attribute name="nbpage"><xsl:value-of select="php:functionString('GetKEnv','PAGEMAX')"/></xsl:attribute></navi>
			<xsl:apply-templates select="item"/>
			<navi><xsl:attribute name="page"><xsl:value-of select="php:functionString('GetKEnv','PAGE')"/></xsl:attribute><xsl:attribute name="nbpage"><xsl:value-of select="php:functionString('GetKEnv','PAGEMAX')"/></xsl:attribute></navi>
		</data>
	</xsl:template>

	<xsl:template match="item">
		<table_zone>
			<head>
				<right>
					<xsl:value-of select="concat(substring(date_input,9,2),'/',substring(date_input,6,2),'/',substring(date_input,1,4))"/>
				</right>
				<left>
					<xsl:if test="email != ''">
						<xsl:attribute name="href">
							<xsl:text>?query=send_mail&amp;key=</xsl:text>
							<xsl:value-of select="id"/>
						</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="name"/>
				</left>
			</head>
			<content>
				<xsl:value-of disable-output-escaping="yes"  select="comment"/>
			</content>
			<actions>
				<xsl:if test="__DEL__">
					<xsl:choose>
						<xsl:when test="$query='del'">
							<action query="submit_del" text='ButtonDeleteC'>
								<xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
							</action>
						</xsl:when>
						<xsl:otherwise>
							<action query="del" text='ButtonDelete'>
								<xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
							</action>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
			</actions>
		</table_zone>
	</xsl:template>
</xsl:stylesheet>

