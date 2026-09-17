<?php

/**
 * Website System Architecture poster from the handwritten draft,
 * filled with the real WBPOS stack (Trusted IT Business Co., Ltd).
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$docs = $root . DIRECTORY_SEPARATOR . 'docs';
if (!is_dir($docs) && !mkdir($docs, 0755, true) && !is_dir($docs)) {
    fwrite(STDERR, "Cannot create docs/\n");
    exit(1);
}

$html = buildHtml();
$htmlPath = $docs . DIRECTORY_SEPARATOR . 'WBPOS-System-Architecture.html';
file_put_contents($htmlPath, $html);

$chrome = findChrome();
$pdfPath = $docs . DIRECTORY_SEPARATOR . 'WBPOS-System-Architecture.pdf';
$pngPath = $docs . DIRECTORY_SEPARATOR . 'wbpos-architecture-network.png';
$docxPath = $docs . DIRECTORY_SEPARATOR . 'WBPOS-System-Architecture.docx';

if ($chrome === null) {
    fwrite(STDERR, "Chrome/Edge not found. HTML written to $htmlPath\n");
    exit(1);
}

$profile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wbpos-arch-chrome';
if (!is_dir($profile)) {
    mkdir($profile, 0755, true);
}

runChrome($chrome, $profile, [
    '--hide-scrollbars',
    '--force-device-scale-factor=2',
    '--window-size=2000,1416',
    '--virtual-time-budget=4000',
    '--screenshot=' . $pngPath,
    htmlFileUrl($htmlPath),
]);

$printHtml = $docs . DIRECTORY_SEPARATOR . 'wbpos-architecture-print.html';
file_put_contents($printHtml, '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
@page{size:A4 landscape;margin:0}html,body{margin:0}img{width:297mm;height:210mm;display:block}
</style></head><body><img src="' . htmlFileUrl($pngPath) . '" alt="Website System Architecture"></body></html>');

runChrome($chrome, $profile, [
    '--print-to-pdf=' . $pdfPath,
    '--no-pdf-header-footer',
    '--print-to-pdf-no-header',
    htmlFileUrl($printHtml),
]);

buildDocx($docxPath, $pngPath);

echo "Wrote:\n  $htmlPath\n  $pdfPath\n  $docxPath\n  $pngPath\n";

function findChrome(): ?string
{
    foreach ([
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
    ] as $path) {
        if (is_file($path)) {
            return $path;
        }
    }

    return null;
}

function htmlFileUrl(string $path): string
{
    $normalized = str_replace('\\', '/', $path);
    if (preg_match('#^[A-Za-z]:#', $normalized)) {
        return 'file:///' . $normalized;
    }

    return 'file://' . $normalized;
}

function runChrome(string $chrome, string $profile, array $args): void
{
    $cmd = array_merge([
        $chrome,
        '--headless=new',
        '--disable-gpu',
        '--allow-file-access-from-files',
        '--user-data-dir=' . $profile,
    ], $args);
    $line = implode(' ', array_map('escapeshellarg', $cmd));
    $out = [];
    $code = 0;
    exec($line . ' 2>&1', $out, $code);
    if ($code !== 0) {
        fwrite(STDERR, "Chrome failed ($code)\n" . implode("\n", $out) . "\n");
        exit(1);
    }
}

function buildHtml(): string
{
    return <<<'HTML'
<!DOCTYPE html>
<html lang="km">
<head>
<meta charset="utf-8">
<title>Website System Architecture — WBPOS</title>
<style>
@page { size: A4 landscape; margin: 0; }
* { box-sizing: border-box; }
@font-face {
  font-family: "KhmerPoster";
  src: url("khmerui.ttf") format("truetype");
  font-weight: 400;
}
@font-face {
  font-family: "KhmerPoster";
  src: url("khmeruib.ttf") format("truetype");
  font-weight: 700;
}
html, body {
  margin: 0;
  padding: 0;
  width: 2000px;
  height: 1414px;
  overflow: hidden;
  background: #fff;
  color: #111;
  font-family: Arial, Helvetica, sans-serif;
  -webkit-print-color-adjust: exact;
  print-color-adjust: exact;
}
.sheet {
  width: 2000px;
  height: 1414px;
  padding: 20px 28px 14px;
  display: flex;
  flex-direction: column;
}
.head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 6px;
}
.topic .label { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
.topic .km {
  font-family: "KhmerPoster", "Khmer UI", "Leelawadee UI", Arial, sans-serif;
  font-size: 20px;
  line-height: 1.45;
}
.topic .en { font-size: 18px; line-height: 1.4; margin-top: 2px; }
.title {
  font-size: 42px;
  font-weight: 800;
  letter-spacing: -0.4px;
  padding-top: 6px;
  white-space: nowrap;
}
.diagram {
  flex: 0 0 520px;
  height: 520px;
}
.diagram svg { width: 100%; height: 100%; display: block; }
.bottom {
  flex: 1;
  display: grid;
  grid-template-columns: 0.72fr 1.28fr;
  gap: 14px;
  min-height: 0;
  margin-top: 8px;
}
.left {
  background: #8b9096;
  border: 2px solid #222;
  padding: 16px 18px 18px;
}
.left h2, .right h2 {
  margin: 0 0 10px;
  font-size: 19px;
  font-weight: 800;
}
.left h2.ii { margin-top: 28px; }
.left ol { margin: 0; padding-left: 24px; }
.left li { font-size: 18px; line-height: 1.85; margin: 6px 0; }
.right { padding: 4px 8px 0 10px; }
.roles {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px 36px;
}
.col-head {
  font-size: 19px;
  font-weight: 800;
  margin: 0 0 10px;
}
.rt {
  color: #0e7490;
  font-weight: 800;
  font-size: 18px;
  margin: 0 0 6px;
}
.rt.red { color: #dc2626; }
.rb {
  margin: 0;
  padding-left: 18px;
  font-size: 16px;
  line-height: 1.45;
}
.rb li { margin: 1px 0; }
.rb ul { margin: 2px 0 0; padding-left: 16px; }
</style>
</head>
<body>
<div class="sheet">
  <header class="head">
    <div class="topic">
      <div class="label">Topic:</div>
      <div class="km"><b>(Khmer)</b> ប្រព័ន្ធលក់ផ្អែកលើគេហទំព័ររបស់ Trusted IT Business Co., Ltd</div>
      <div class="en"><b>(English)</b> Web-based POS System for Trusted IT Business Co., Ltd</div>
    </div>
    <div class="title">Website System Architecture</div>
  </header>

  <div class="diagram">
    <svg viewBox="0 0 1944 520" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="storeBg" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#f7efd8"/>
          <stop offset="100%" stop-color="#e7d6aa"/>
        </linearGradient>
        <filter id="sh" x="-12%" y="-12%" width="130%" height="140%">
          <feDropShadow dx="1" dy="3" stdDeviation="2.2" flood-opacity="0.2"/>
        </filter>
      </defs>

      <!-- POS store -->
      <rect x="20" y="36" width="620" height="360" rx="10" fill="url(#storeBg)" stroke="#c4b183"/>
      <text x="330" y="68" text-anchor="middle" font-family="Arial" font-size="20" font-weight="700" fill="#4b5563">POS Store (WBPOS)</text>

      <g font-family="Arial" text-anchor="middle">
        <g transform="translate(48,88)">
          <rect width="168" height="168" rx="12" fill="#eff6ff" stroke="#1d4ed8" stroke-width="2"/>
          <circle cx="84" cy="52" r="22" fill="#1d4ed8"/>
          <path d="M44 118c0-28 18-44 40-44s40 16 40 44" fill="#1d4ed8"/>
          <rect x="72" y="70" width="24" height="8" rx="2" fill="#93c5fd"/>
          <text x="84" y="148" font-size="13" font-weight="700" fill="#1e3a8a">SUPER ADMIN</text>
          <text x="84" y="164" font-size="10" fill="#64748b">Windows / Chrome</text>
        </g>
        <g transform="translate(226,88)">
          <rect width="168" height="168" rx="12" fill="#f0fdf4" stroke="#15803d" stroke-width="2"/>
          <circle cx="68" cy="50" r="16" fill="#15803d"/>
          <path d="M40 108c0-20 12-32 28-32s28 12 28 32" fill="#15803d"/>
          <circle cx="104" cy="54" r="16" fill="#22c55e"/>
          <path d="M76 112c0-20 12-32 28-32s28 12 28 32" fill="#22c55e"/>
          <text x="84" y="148" font-size="13" font-weight="700" fill="#14532d">SHOP OWNER</text>
          <text x="84" y="164" font-size="10" fill="#64748b">Windows / Chrome</text>
        </g>
        <g transform="translate(404,88)">
          <rect width="168" height="168" rx="12" fill="#fff7ed" stroke="#c2410c" stroke-width="2"/>
          <circle cx="84" cy="52" r="22" fill="#c2410c"/>
          <path d="M44 118c0-28 18-44 40-44s40 16 40 44" fill="#c2410c"/>
          <rect x="58" y="78" width="52" height="10" rx="2" fill="#fdba74"/>
          <text x="84" y="148" font-size="13" font-weight="700" fill="#9a3412">CASHIER / SALE</text>
          <text x="84" y="164" font-size="10" fill="#64748b">Windows / Chrome</text>
        </g>
      </g>
      <text x="330" y="292" text-anchor="middle" font-family="Arial" font-size="13" fill="#334155">Admin · Sale · Store  —  browser only, no POS terminal</text>
      <rect x="150" y="300" width="360" height="28" rx="4" fill="#fff" stroke="#94a3b8"/>
      <text x="330" y="320" text-anchor="middle" font-family="Arial" font-size="13">HTTPS from any PC / laptop</text>

      <!-- arrows to internet -->
      <path d="M640 210 H760" stroke="#0d9488" stroke-width="4" fill="none"/>
      <polygon points="760,210 744,200 744,220" fill="#0d9488"/>

      <!-- Internet -->
      <g transform="translate(770,118)" filter="url(#sh)">
        <path d="M70 90c-22-40 16-70 50-46 16-40 80-40 94 4 40-14 70 22 44 54 30 10 28 58-8 64-10 32-70 40-90 8-28 26-76 8-76-22-32 8-50-22-14-62z" fill="#7ec8e3" stroke="#4aa3c7" stroke-width="2"/>
        <text x="118" y="102" text-anchor="middle" font-family="Arial" font-size="20" font-weight="700" fill="#0f4c75">Internet</text>
      </g>

      <!-- WAN lightning -->
      <path d="M1028 210 L1088 168 L1074 168 L1148 110" fill="none" stroke="#eab308" stroke-width="4"/>
      <polygon points="1148,110 1132,124 1158,122" fill="#eab308"/>
      <text x="1088" y="150" font-family="Arial" font-size="13" font-weight="700" fill="#dc2626">WAN</text>

      <!-- Hosting server -->
      <g transform="translate(1180,70)" font-family="Arial">
        <text x="110" y="18" text-anchor="middle" font-size="14" font-weight="700">1  Shared Hosting</text>
        <g filter="url(#sh)">
          <polygon points="70,48 150,48 172,28 92,28" fill="#9fd0f3"/>
          <polygon points="150,48 172,28 172,188 150,208" fill="#2b6fa3"/>
          <rect x="70" y="48" width="80" height="160" fill="#5aa4d6" stroke="#3b82b0"/>
        </g>
        <circle cx="110" cy="108" r="22" fill="#163e66"/>
        <text x="110" y="116" text-anchor="middle" font-size="20" font-weight="700" fill="#fff">1</text>
        <circle cx="110" cy="168" r="16" fill="#fff" stroke="#0f4c75"/>
        <path d="M102 168 h16 M110 160 v16 M98 164 q12 8 24 0" fill="none" stroke="#0f4c75" stroke-width="2"/>
        <text x="110" y="236" text-anchor="middle" font-size="14" font-weight="700">pos.chhit.com</text>
        <rect x="40" y="246" width="140" height="22" rx="3" fill="#fff" stroke="#94a3b8"/>
        <text x="110" y="262" text-anchor="middle" font-size="11">DirectAdmin · PHP · SSL</text>
      </g>

      <!-- MySQL database -->
      <g transform="translate(1188,348)" font-family="Arial" text-anchor="middle">
        <ellipse cx="70" cy="14" rx="52" ry="12" fill="#0e7490"/>
        <rect x="18" y="14" width="104" height="36" fill="#14b8a6"/>
        <ellipse cx="70" cy="50" rx="52" ry="12" fill="#0f766e"/>
        <text x="70" y="40" font-size="11" font-weight="700" fill="#fff">pos_wbpos</text>
        <text x="70" y="72" font-size="11" fill="#334155">MySQL · 55 tables</text>
      </g>

      <!-- Client PCs -->
      <g transform="translate(1488,86)" font-family="Arial">
        <text x="150" y="18" text-anchor="middle" font-size="14" font-weight="700">2  Client</text>
        <line x1="0" y1="150" x2="40" y2="150" stroke="#ca8a04" stroke-width="3"/>
        <g filter="url(#sh)">
          <g transform="translate(48,48)">
            <rect x="8" y="0" width="70" height="48" fill="#efe4c4" stroke="#b9a36b"/>
            <rect x="16" y="8" width="54" height="32" fill="#7dd3fc"/>
            <rect x="30" y="48" width="26" height="10" fill="#d6c49a"/>
            <rect x="12" y="58" width="62" height="8" fill="#c4b184"/>
          </g>
          <g transform="translate(148,48)">
            <rect x="8" y="0" width="70" height="48" fill="#efe4c4" stroke="#b9a36b"/>
            <rect x="16" y="8" width="54" height="32" fill="#7dd3fc"/>
            <rect x="30" y="48" width="26" height="10" fill="#d6c49a"/>
            <rect x="12" y="58" width="62" height="8" fill="#c4b184"/>
          </g>
        </g>
        <text x="150" y="150" text-anchor="middle" font-size="14" font-weight="700">Windows 11 · Chrome</text>
        <rect x="70" y="164" width="160" height="22" rx="3" fill="#fff" stroke="#94a3b8"/>
        <text x="150" y="180" text-anchor="middle" font-size="12">pos.chhit.com</text>
      </g>

      <text x="972" y="430" text-anchor="middle" font-family="Arial" font-size="14" fill="#475569">Store staff (Chrome)  →  Internet  →  pos.chhit.com (shared hosting)  →  MySQL pos_wbpos</text>
    </svg>
  </div>

  <div class="bottom">
    <aside class="left">
      <h2>I. Technology and scope of work briefly:</h2>
      <ol>
        <li>Web-based Point of Sale (browser only)</li>
        <li>Shared web hosting (DirectAdmin control panel)</li>
        <li>MySQL database (pos_wbpos)</li>
        <li>HTTPS / SSL certificate</li>
        <li>Login by role: Super Admin, Shop Owner, Cashier</li>
      </ol>
      <h2 class="ii">II. Software Project:</h2>
      <ol>
        <li>PHP 8.2 / CodeIgniter 4 (WBPOS)</li>
        <li>MySQL — database pos_wbpos</li>
        <li>DirectAdmin (Domains, SSL, FTP, phpMyAdmin)</li>
        <li>XAMPP + Visual Studio Code (local)</li>
        <li>Google Chrome</li>
      </ol>
    </aside>
    <section class="right">
      <div class="roles">
        <div>
          <h2 class="col-head">III. Server</h2>
          <div class="rt red">1. Shared hosting (pos.chhit.com)</div>
          <ul class="rb">
            <li>+ Panel: DirectAdmin control panel</li>
            <li>+ Domain: pos.chhit.com</li>
            <li>+ IP Address: 160.30.208.13</li>
            <li>+ Database: MySQL pos_wbpos (55 tables)</li>
            <li>+ Role:
              <ul>
                <li>Host WBPOS website (PHP)</li>
                <li>MySQL + phpMyAdmin</li>
                <li>SSL/TLS, FTP, DNS</li>
              </ul>
            </li>
          </ul>
        </div>
        <div>
          <h2 class="col-head">IV. Client</h2>
          <div class="rt">2. Client (PC / laptop)</div>
          <ul class="rb">
            <li>+ OS: Windows 11</li>
            <li>+ Browser: Google Chrome</li>
            <li>+ IP Address: DHCP (LAN / Internet)</li>
            <li>+ RAM: 8 GB</li>
            <li>+ Role:
              <ul>
                <li>Open https://pos.chhit.com</li>
                <li>Super Admin / Shop Owner / Cashier</li>
                <li>Sales, stock, reports in the browser</li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </section>
  </div>
</div>
</body>
</html>
HTML;
}

function buildDocx(string $docxPath, string $png): void
{
    if (!is_file($png)) {
        fwrite(STDERR, "PNG missing\n");
        exit(1);
    }
    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wbpos-docx-' . bin2hex(random_bytes(4));
    mkdir($tmp . '/word/media', 0755, true);
    mkdir($tmp . '/word/_rels', 0755, true);
    mkdir($tmp . '/_rels', 0755, true);
    mkdir($tmp . '/docProps', 0755, true);
    copy($png, $tmp . '/word/media/image1.png');
    file_put_contents($tmp . '/[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Default Extension="png" ContentType="image/png"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>');
    file_put_contents($tmp . '/_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>');
    file_put_contents($tmp . '/word/_rels/document.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image1.png"/>
</Relationships>');
    $now = gmdate('Y-m-d\TH:i:s\Z');
    file_put_contents($tmp . '/docProps/core.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>Website System Architecture — WBPOS</dc:title>
  <dc:creator>Trusted IT Business Co., Ltd</dc:creator>
  <dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>
</cp:coreProperties>');
    file_put_contents($tmp . '/docProps/app.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>WBPOS</Application></Properties>');
    $cx = 10200000;
    $cy = 7215000;
    file_put_contents($tmp . '/word/document.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
  <w:body>
    <w:p><w:r><w:drawing>
      <wp:inline distT="0" distB="0" distL="0" distR="0">
        <wp:extent cx="' . $cx . '" cy="' . $cy . '"/>
        <wp:docPr id="1" name="Website System Architecture"/>
        <a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
          <pic:pic>
            <pic:nvPicPr><pic:cNvPr id="1" name="arch.png"/><pic:cNvPicPr/></pic:nvPicPr>
            <pic:blipFill><a:blip r:embed="rId1"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>
            <pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $cx . '" cy="' . $cy . '"/></a:xfrm>
            <a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>
          </pic:pic>
        </a:graphicData></a:graphic>
      </wp:inline>
    </w:drawing></w:r></w:p>
    <w:sectPr>
      <w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/>
      <w:pgMar w:top="360" w:right="360" w:bottom="360" w:left="360"/>
    </w:sectPr>
  </w:body>
</w:document>');
    $zip = new ZipArchive();
    if ($zip->open($docxPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        fwrite(STDERR, "Cannot write $docxPath\n");
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
}
