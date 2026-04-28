<?php
/**
 * @var array $barcode_config
 * @var array $items
 */

use App\Libraries\Barcode_lib;

$barcode_lib = new Barcode_lib();
?>

<!doctype html>
<html lang="<?= current_language_code() ?>">

<head>
    <meta charset="utf-8">
    <title><?= lang('Items.generate_barcodes') ?></title>
    <link rel="stylesheet" href="<?= base_url() ?>css/barcode_font.css">
    <style>
        :root {
            color-scheme: light;
        }

        body {
            margin: 0;
            background: #f8fafc;
            color: #0f172a;
        }

        .barcode-sheet {
            max-width: 1100px;
            margin: 18px auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
            padding: 14px;
        }

        .barcode-sheet table {
            width: <?= $barcode_config['barcode_page_width'] . '%' ?>;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: <?= $barcode_config['barcode_page_cellspacing'] ?>px;
        }

        .barcode-sheet td {
            text-align: center;
            vertical-align: top;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            padding: 10px 8px;
        }

        .barcode svg {
            height: <?= $barcode_config['barcode_height'] ?>px;
            width: <?= $barcode_config['barcode_width'] ?>px;
        }

        .barcode {
            display: inline-block;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .barcode-sheet {
                max-width: none;
                margin: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                padding: 0;
            }

            .barcode-sheet td {
                border: 0;
                border-radius: 0;
                padding: 0;
                background: #ffffff;
            }
        }
    </style>
</head>

<body class=<?= 'font_' . $barcode_lib->get_font_name($barcode_config['barcode_font']) ?> style="font-size: <?= $barcode_config['barcode_font_size'] ?>px;">
    <div class="barcode-sheet">
        <table>
            <tr>
                <?php
                $count = 0;
                foreach ($items as $item) {
                    if ($count % $barcode_config['barcode_num_in_row'] == 0 && $count != 0) {
                        echo '</tr><tr>';
                    }
                    echo '<td>' . $barcode_lib->display_barcode($item, $barcode_config) . '</td>';
                    $count++;
                }
                ?>
            </tr>
        </table>
    </div>
</body>

</html>
