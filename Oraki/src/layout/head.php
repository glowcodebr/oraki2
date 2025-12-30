<?php
// Define variáveis padrão se não existirem
$pageTitle = isset($title) ? $title : 'Oraki.';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
    </style>
    <script>
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                milk: '#FAF7F0',
                astral: '#16052B',
                crowberry: '#220055',
                galactic: '#472E97',
                summer: '#EEAA44',
                palm: '#EDD69D'
              }
            }
          }
        }
    </script>
</head>
<body class="bg-milk text-astral font-sans antialiased flex flex-col h-screen md:flex-row">