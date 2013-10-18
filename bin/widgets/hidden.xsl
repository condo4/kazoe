<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" />

	<xsl:template match="/dataset">
		<xsl:apply-templates select="./field"/>
	</xsl:template>

	<xsl:template match="field">
		<xsl:if test="./@name=$name">
			<xsl:apply-templates select="widget_hidden"/>
		</xsl:if>
	</xsl:template>

	<xsl:template match="widget_hidden">
		<div class='hiddenid'>
			<input type='hidden'>
				<xsl:if test="../value">
					<xsl:attribute name="value"><xsl:value-of select="../value"/></xsl:attribute>
				</xsl:if>
				<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
			</input>
		</div>
	</xsl:template>
</xsl:stylesheet>
