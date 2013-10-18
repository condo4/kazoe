<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" encoding="UTF-8" indent="yes" />
    <xsl:param name="name"/>
    <xsl:param name="lng"/>
    <xsl:param name="lngdef"/>
    
    <xsl:template match="/dataset">
		<xsl:apply-templates select="./field"/>
    </xsl:template>
    
    <xsl:template match="field">
        <xsl:if test="./@name=$name">
            <fieldset>
                <legend><xsl:value-of select='legend' /></legend>
                <xsl:apply-templates select="widget_picture"/>
            </fieldset>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="widget_picture">
	
		<xsl:choose>
			<xsl:when test="../value">
				<xsl:value-of select="../value"/>
			</xsl:when>
			<xsl:otherwise>
				<input type='hidden' name='MAX_FILE_SIZE'>
					<xsl:attribute name="value"><xsl:value-of select="./@maxsize"/></xsl:attribute>
				</input>
				<input type='file' maxlength='512' value=''>
					<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
					<xsl:attribute name="size"><xsl:value-of select="./@size"/></xsl:attribute>
				</input>
			</xsl:otherwise>
		</xsl:choose>


    </xsl:template>
</xsl:stylesheet>
