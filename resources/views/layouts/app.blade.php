<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadyEat - @yield('title', 'Home')</title>
    
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'] },
            colors: {
              primary: '#F97316', // orange-500
              secondary: '#FBBF24', // amber-400
              dark: '#111827', // gray-900
            }
          }
        }
      }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased">
    
    @yield('content')

    </body>
</html>