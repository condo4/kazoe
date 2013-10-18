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
                <xsl:apply-templates select="widget_title"/>
            </fieldset>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="widget_title">
        <input type='text' maxlength='512'>
            <xsl:if test="../value">
                <xsl:attribute name="value"><xsl:value-of select="../value"/></xsl:attribute>
            </xsl:if>
            <xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
            <xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
        </input>
    </xsl:template>
</xsl:stylesheet>
