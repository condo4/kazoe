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
                <xsl:apply-templates select="widget_captcha"/>
            </fieldset>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="widget_captcha">
        <table>
            <tr>
                <td>
                    <img src='kazoe/lib/crypt/cryptographp.php?cfg=0&amp;'>
                        <xsl:attribute name="id"><xsl:value-of select="../@name"/></xsl:attribute>
                    </img>
                </td>
                <td>
                    <a title='' style="cursor:pointer">
                        <xsl:attribute name="onclick">
                            <xsl:text>javascript:document.images.</xsl:text>
                            <xsl:value-of select="../@name"/>
                            <xsl:text>.src='kazoe/lib/crypt/cryptographp.php?cfg=0&amp;&amp;'+Math.round(Math.random(0)*1000)+1</xsl:text>
                        </xsl:attribute>
                        <img src="kazoe/lib/crypt/images/reload.png" />
                    </a>
                </td>
            </tr>
        </table>
        Avant de valider recopiez le code précédent ici :
        <input size="20" name="cryptogramme" type="text" />
    </xsl:template>
</xsl:stylesheet>
