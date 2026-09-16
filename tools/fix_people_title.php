<?php
$p = dirname(__DIR__) . '/app/Views/people/manage.php';
$c = file_get_contents($p);
$old = '<?= ucfirst($controller_name) ?>';
$new = '<?= esc(lang(\'Module.\' . $controller_name)) ?>';
if (!str_contains($c, $old)) {
    fwrite(STDERR, "pattern not found\n");
    exit(1);
}
file_put_contents($p, str_replace($old, $new, $c));
echo "done\n";
