<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oraki - Acesso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
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
<body class="bg-astral text-milk font-sans antialiased h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-crowberry rounded-full blur-[100px] opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-galactic rounded-full blur-[80px] opacity-30"></div>
    </div>