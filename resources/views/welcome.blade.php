<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Elecciones 2024</title>
</head>

<body>
<section class="min-h-screen bg-gradient-to-b from-blue-600 via-blue-500 to-blue-400 flex items-center">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="mb-12">
            <h1 class="text-5xl font-bold text-white mb-5">
                Sistema Electoral 2024
            </h1>
            <p class="text-xl text-white mb-8">
                Sistema de control y gestión de resultados electorales
            </p>
        </div>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="/admin"
               class="bg-white text-blue-600 px-8 py-3 rounded-lg shadow-lg hover:bg-blue-50 transition-colors duration-200 font-semibold text-lg w-full sm:w-auto">
                Panel de Administración
            </a>
            <a href="/resultados"
               class="bg-blue-600 text-white px-8 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition-colors duration-200 font-semibold text-lg w-full sm:w-auto">
                Resultados Electorales
            </a>
            <a href="/talonador"
               class="bg-blue-800 text-white px-8 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition-colors duration-200 font-semibold text-lg w-full sm:w-auto">
                Acceso Talonador
            </a>
        </div>
    </div>
</section>
</body>
</html>
