<?php
/**
 * Fill empty/missing Khmer translations from English keys.
 * Run: php tools/apply_km_translations.php
 */

$root = dirname(__DIR__) . '/app/Language';
$enDir = $root . '/en';
$kmDir = $root . '/km';

/** @return array<string,string> */
function load_lang(string $file): array
{
    $data = include $file;
    return is_array($data) ? $data : [];
}

function export_lang(array $data): string
{
    ksort($data);
    $out = "<?php\n\nreturn [\n";
    $maxKey = 0;
    foreach (array_keys($data) as $k) {
        $maxKey = max($maxKey, strlen((string)$k));
    }
    $pad = min(40, $maxKey + 2);
    foreach ($data as $key => $value) {
        $k = var_export((string)$key, true);
        $v = var_export((string)$value, true);
        $spaces = str_repeat(' ', max(1, $pad - strlen((string)$key)));
        $out .= "    {$k}{$spaces}=> {$v},\n";
    }
    $out .= "];\n";
    return $out;
}

/**
 * Khmer translations keyed by "File.php|key"
 * Only entries that need filling are listed; existing good Khmer is kept.
 *
 * @return array<string,string>
 */
function translations(): array
{
    // Loaded from companion JSON for size; fallback inline Common first.
    $json = __DIR__ . '/km_translations.json';
    if (is_file($json)) {
        $data = json_decode((string)file_get_contents($json), true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [];
}

$tr = translations();
if ($tr === []) {
    fwrite(STDERR, "Missing tools/km_translations.json\n");
    exit(1);
}

$updatedFiles = 0;
$updatedKeys = 0;

foreach (glob($enDir . '/*.php') as $enFile) {
    $name = basename($enFile);
    $kmFile = $kmDir . '/' . $name;
    $en = load_lang($enFile);
    $km = is_file($kmFile) ? load_lang($kmFile) : [];
    $changed = false;

    foreach ($en as $key => $enVal) {
        if (!is_string($enVal)) {
            continue;
        }
        $mapKey = $name . '|' . $key;
        if (!isset($tr[$mapKey])) {
            continue;
        }
        $current = array_key_exists($key, $km) ? trim((string)$km[$key]) : '';
        $enTrim = trim($enVal);
        // Fill when missing, empty, or still English duplicate
        if ($current === '' || $current === $enTrim) {
            $km[$key] = $tr[$mapKey];
            $changed = true;
            $updatedKeys++;
        }
    }

    // Ensure every EN key exists in KM (even if we have no translation yet keep previous)
    foreach ($en as $key => $enVal) {
        if (!array_key_exists($key, $km)) {
            $km[$key] = is_string($enVal) ? $enVal : '';
            $changed = true;
        }
    }

    if ($changed) {
        file_put_contents($kmFile, export_lang($km));
        $updatedFiles++;
        echo "Updated $name\n";
    }
}

echo "Done. files=$updatedFiles keys=$updatedKeys\n";
