<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()" />
        </xsl:copy>
    </xsl:template>
    <xsl:template match="row">
	    <currency><xsl:apply-templates select="@*|node()" /></currency>
    </xsl:template>
    <xsl:template match="results">
	    <currencies><xsl:apply-templates select="@*|node()" /></currencies>
    </xsl:template>
    <xsl:template match="col0">
	    <code><xsl:apply-templates select="@*|node()" /></code>
    </xsl:template>
    
    <xsl:template match="col1">
	    <rate><xsl:apply-templates select="@*|node()" /></rate>
    </xsl:template>
    <xsl:template match="col2">
	    <date><xsl:apply-templates select="@*|node()" /></date>
    </xsl:template>
    <xsl:template match="col3">
	    <time><xsl:apply-templates select="@*|node()" /></time>
    </xsl:template>
</xsl:stylesheet>
