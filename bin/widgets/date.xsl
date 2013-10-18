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
            <select>
                <xsl:attribute name="name"><xsl:value-of select="@name"/>_day</xsl:attribute>
                <xsl:attribute name="class">day</xsl:attribute>
                <xsl:for-each select="./widget_date/days/day">
                    <option>
                        <xsl:attribute name="value"><xsl:value-of select="@name"/></xsl:attribute>
                        <xsl:if test="@sel='true'">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="@value" />
                    </option>
                </xsl:for-each>
            </select>
            <select>
                <xsl:attribute name="name"><xsl:value-of select="@name"/>_month</xsl:attribute>
                <xsl:attribute name="class">month</xsl:attribute>
                <xsl:for-each select="./widget_date/months/month">
                    <option>
                        <xsl:attribute name="value"><xsl:value-of select="@name"/></xsl:attribute>
                        <xsl:if test="@sel='true'">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="@value" />
                    </option>
                </xsl:for-each>
            </select>
            <select>
                <xsl:attribute name="name"><xsl:value-of select="@name"/>_year</xsl:attribute>
                <xsl:attribute name="class">year</xsl:attribute>
                <xsl:for-each select="./widget_date/years/year">
                    <option>
                        <xsl:attribute name="value"><xsl:value-of select="@name"/></xsl:attribute>
                        <xsl:if test="@sel='true'">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="@value" />
                    </option>
                </xsl:for-each>
            </select>
        </fieldset>
       </xsl:if>
    </xsl:template>
    
</xsl:stylesheet>
