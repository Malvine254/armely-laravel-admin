<?php

declare(strict_types=1);

$pdo = new PDO('mysql:host=127.0.0.1;dbname=armely_store', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
SELECT
  (SELECT COUNT(*) FROM jobs WHERE queue='products-sync') AS queued_jobs,
  (SELECT COUNT(*) FROM failed_jobs) AS failed_jobs,
  (SELECT COUNT(*) FROM products WHERE vendor_id='TD SYNNEX' AND images IS NOT NULL AND images <> '[]' AND images <> '') AS with_images,
  (SELECT COUNT(*) FROM products WHERE vendor_id='TD SYNNEX') AS total_td_synnex
";

$row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
echo json_encode($row, JSON_UNESCAPED_SLASHES), PHP_EOL;
