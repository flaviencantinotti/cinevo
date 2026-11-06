<?php
$meta += ['description' => '', 'robots' => '', 'classe_body' => ''];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ($meta['description'] !== ''): ?>
    <meta name="description" content="<?= htmlspecialchars($meta['description'], ENT_COMPAT | ENT_SUBSTITUTE) ?>">
<?php endif; ?>
<?php if ($meta['robots'] !== ''): ?>
    <meta name="robots" content="<?= htmlspecialchars($meta['robots']) ?>">
<?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title><?= htmlspecialchars($meta['titre'], ENT_NOQUOTES | ENT_SUBSTITUTE) ?></title>
</head>
<body<?= $meta['classe_body'] !== '' ? ' class="' . htmlspecialchars($meta['classe_body']) . '"' : '' ?>>

<?php include __DIR__ . '/header.php'; ?>
