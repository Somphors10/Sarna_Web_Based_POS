<?php

/**
 * Editable Word document: real Khmer text (Khmer OS Siemreap) + diagram picture.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$docs = $root . DIRECTORY_SEPARATOR . 'docs';
$png = $docs . DIRECTORY_SEPARATOR . 'wbpos-architecture-network.png';
$docx = $docs . DIRECTORY_SEPARATOR . 'WBPOS-Website-Architecture.docx';

if (!is_file($png)) {
    fwrite(STDERR, "Run gen_wbpos_architecture_docs.php first so the diagram PNG exists.\n");
    exit(1);
}

$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wbpos-edit-docx-' . bin2hex(random_bytes(4));
foreach (['word/media', 'word/_rels', '_rels', 'docProps'] as $dir) {
    mkdir($tmp . '/' . $dir, 0755, true);
}
copy($png, $tmp . '/word/media/diagram.png');

file_put_contents($tmp . '/[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Default Extension="png" ContentType="image/png"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
  <Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML);

file_put_contents($tmp . '/_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML);

file_put_contents($tmp . '/word/_rels/document.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/diagram.png"/>
</Relationships>
XML);

file_put_contents($tmp . '/word/styles.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:docDefaults>
    <w:rPrDefault>
      <w:rPr>
        <w:rFonts w:ascii="Calibri" w:hAnsi="Calibri" w:eastAsia="Khmer OS Siemreap" w:cs="Khmer OS Siemreap"/>
        <w:sz w:val="22"/><w:szCs w:val="22"/>
      </w:rPr>
    </w:rPrDefault>
    <w:pPrDefault><w:pPr><w:spacing w:after="80"/></w:pPr></w:pPrDefault>
  </w:docDefaults>
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal">
    <w:name w:val="Normal"/>
    <w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Title">
    <w:name w:val="Title"/>
    <w:pPr><w:jc w:val="right"/><w:spacing w:after="120"/></w:pPr>
    <w:rPr><w:b/><w:sz w:val="40"/><w:szCs w:val="40"/><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading1">
    <w:name w:val="heading 1"/>
    <w:pPr><w:spacing w:before="160" w:after="80"/></w:pPr>
    <w:rPr><w:b/><w:sz w:val="26"/><w:color w:val="111111"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading2">
    <w:name w:val="heading 2"/>
    <w:pPr><w:spacing w:before="80" w:after="40"/></w:pPr>
    <w:rPr><w:b/><w:sz w:val="24"/><w:color w:val="0E7490"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="KhmerBody">
    <w:name w:val="Khmer Body"/>
    <w:rPr>
      <w:rFonts w:ascii="Khmer OS Siemreap" w:hAnsi="Khmer OS Siemreap" w:eastAsia="Khmer OS Siemreap" w:cs="Khmer OS Siemreap"/>
      <w:sz w:val="28"/><w:szCs w:val="28"/>
      <w:lang w:val="en-US" w:eastAsia="km-KH" w:bidi="km-KH"/>
    </w:rPr>
  </w:style>
</w:styles>
XML);

file_put_contents($tmp . '/word/numbering.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:abstractNum w:abstractNumId="0">
    <w:multiLevelType w:val="hybridMultilevel"/>
    <w:lvl w:ilvl="0">
      <w:start w:val="1"/><w:numFmt w:val="decimal"/><w:lvlText w:val="%1."/><w:lvlJc w:val="left"/>
      <w:pPr><w:ind w:left="360" w:hanging="360"/></w:pPr>
    </w:lvl>
  </w:abstractNum>
  <w:abstractNum w:abstractNumId="1">
    <w:multiLevelType w:val="hybridMultilevel"/>
    <w:lvl w:ilvl="0">
      <w:start w:val="1"/><w:numFmt w:val="decimal"/><w:lvlText w:val="%1."/><w:lvlJc w:val="left"/>
      <w:pPr><w:ind w:left="360" w:hanging="360"/></w:pPr>
    </w:lvl>
  </w:abstractNum>
  <w:abstractNum w:abstractNumId="2">
    <w:multiLevelType w:val="hybridMultilevel"/>
    <w:lvl w:ilvl="0">
      <w:start w:val="1"/><w:numFmt w:val="bullet"/><w:lvlText w:val="•"/><w:lvlJc w:val="left"/>
      <w:pPr><w:ind w:left="360" w:hanging="360"/></w:pPr>
    </w:lvl>
  </w:abstractNum>
  <w:num w:numId="1"><w:abstractNumId w:val="0"/></w:num>
  <w:num w:numId="2"><w:abstractNumId w:val="1"/></w:num>
  <w:num w:numId="3"><w:abstractNumId w:val="2"/></w:num>
</w:numbering>
XML);

$now = gmdate('Y-m-d\TH:i:s\Z');
file_put_contents($tmp . '/docProps/core.xml', <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>Website System Architecture</dc:title>
  <dc:subject>Web-based POS System for Trusted IT Business Co., Ltd</dc:subject>
  <dc:creator>Trusted IT Business Co., Ltd</dc:creator>
  <dcterms:created xsi:type="dcterms:W3CDTF">$now</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">$now</dcterms:modified>
</cp:coreProperties>
XML);
file_put_contents($tmp . '/docProps/app.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>WBPOS</Application></Properties>');

$km = htmlspecialchars('ប្រព័ន្ធលក់ផ្អែកលើគេហទំព័ររបស់ Trusted IT Business Co., Ltd', ENT_XML1);
$imgCx = 10200000;
$imgCy = 7215000;

$document = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
  <w:body>
    <w:p><w:pPr><w:pStyle w:val="Title"/></w:pPr><w:r><w:t>Website System Architecture</w:t></w:r></w:p>

    <w:p><w:r><w:rPr><w:b/><w:sz w:val="24"/></w:rPr><w:t>Topic:</w:t></w:r></w:p>

    <w:p>
      <w:pPr><w:pStyle w:val="KhmerBody"/></w:pPr>
      <w:r><w:rPr><w:b/><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/></w:rPr><w:t xml:space="preserve">(Khmer) </w:t></w:r>
      <w:r>
        <w:rPr>
          <w:rFonts w:ascii="Khmer OS Siemreap" w:hAnsi="Khmer OS Siemreap" w:eastAsia="Khmer OS Siemreap" w:cs="Khmer OS Siemreap"/>
          <w:sz w:val="28"/><w:szCs w:val="28"/>
          <w:lang w:eastAsia="km-KH"/>
        </w:rPr>
        <w:t>$km</w:t>
      </w:r>
    </w:p>
    <w:p>
      <w:r><w:rPr><w:b/></w:rPr><w:t xml:space="preserve">(English) </w:t></w:r>
      <w:r><w:t>Web-based POS System for Trusted IT Business Co., Ltd</w:t></w:r>
    </w:p>
    <w:p>
      <w:r><w:rPr><w:i/><w:sz w:val="18"/><w:color w:val="666666"/></w:rPr>
      <w:t>Select the Khmer line → Home → Font → try “Khmer OS Siemreap”, “Khmer OS Battambang”, “DaunPenh”, or “Moul”.</w:t>
    </w:r></w:p>

    <w:p>
      <w:r>
        <w:drawing>
          <wp:inline distT="0" distB="0" distL="0" distR="0">
            <wp:extent cx="$imgCx" cy="$imgCy"/>
            <wp:docPr id="1" name="Diagram"/>
            <a:graphic>
              <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
                <pic:pic>
                  <pic:nvPicPr><pic:cNvPr id="1" name="diagram.png"/><pic:cNvPicPr/></pic:nvPicPr>
                  <pic:blipFill><a:blip r:embed="rId3"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>
                  <pic:spPr>
                    <a:xfrm><a:off x="0" y="0"/><a:ext cx="$imgCx" cy="$imgCy"/></a:xfrm>
                    <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
                  </pic:spPr>
                </pic:pic>
              </a:graphicData>
            </a:graphic>
          </wp:inline>
        </w:drawing>
      </w:r>
    </w:p>

    <w:p><w:r><w:br w:type="page"/></w:r></w:p>

    <w:p><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:r><w:t>I. Technology and scope of work briefly</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Web-based Point of Sale (browser only)</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Shared web hosting (DirectAdmin control panel)</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>MySQL database (pos_wbpos)</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>HTTPS / SSL certificate</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr></w:pPr><w:r><w:t>Login by role: Super Admin, Shop Owner, Cashier</w:t></w:r></w:p>

    <w:p><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:r><w:t>II. Software Project</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>PHP 8.2 / CodeIgniter 4 (WBPOS)</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>MySQL — database pos_wbpos</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>DirectAdmin (Domains, SSL, FTP, phpMyAdmin)</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>XAMPP + Visual Studio Code (local)</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="2"/></w:numPr></w:pPr><w:r><w:t>Google Chrome</w:t></w:r></w:p>

    <w:p><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:r><w:t>III. Server</w:t></w:r></w:p>
    <w:p><w:pPr><w:pStyle w:val="Heading2"/></w:pPr><w:r><w:rPr><w:color w:val="DC2626"/></w:rPr><w:t>1. Shared hosting (pos.chhit.com)</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ Panel: DirectAdmin control panel</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ Domain: pos.chhit.com</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ IP Address: 160.30.208.13</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ Database: MySQL pos_wbpos (55 tables)</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ Role:</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="3"/></w:numPr></w:pPr><w:r><w:t>Host WBPOS website (PHP)</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="3"/></w:numPr></w:pPr><w:r><w:t>MySQL + phpMyAdmin</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="3"/></w:numPr></w:pPr><w:r><w:t>SSL/TLS, FTP, DNS</w:t></w:r></w:p>

    <w:p><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:r><w:t>IV. Client</w:t></w:r></w:p>
    <w:p><w:pPr><w:pStyle w:val="Heading2"/></w:pPr><w:r><w:t>2. Client (PC / laptop)</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ OS: Windows 11</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ Browser: Google Chrome</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ IP Address: DHCP (LAN / Internet)</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ RAM: 8 GB</w:t></w:r></w:p>
    <w:p><w:r><w:t>+ Role:</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="3"/></w:numPr></w:pPr><w:r><w:t>Open https://pos.chhit.com</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="3"/></w:numPr></w:pPr><w:r><w:t>Super Admin / Shop Owner / Cashier</w:t></w:r></w:p>
    <w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="3"/></w:numPr></w:pPr><w:r><w:t>Sales, stock, reports in the browser</w:t></w:r></w:p>

    <w:sectPr>
      <w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/>
      <w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720"/>
    </w:sectPr>
  </w:body>
</w:document>
XML;

file_put_contents($tmp . '/word/document.xml', $document);

$zip = new ZipArchive();
if ($zip->open($docx, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Cannot write $docx\n");
    exit(1);
}
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmp, FilesystemIterator::SKIP_DOTS));
foreach ($files as $file) {
    if ($file->isDir()) {
        continue;
    }
    $full = $file->getPathname();
    $zip->addFile($full, str_replace('\\', '/', substr($full, strlen($tmp) + 1)));
}
$zip->close();

echo "Wrote editable Word:\n  $docx\n";
echo "Draw.io file:\n  " . $docs . DIRECTORY_SEPARATOR . "WBPOS-Website-System-Architecture.drawio\n";
