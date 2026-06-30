<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login RT - Layanan Desa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
    </style>
</head>
<body class="antialiased text-gray-900 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="w-20 h-20 mx-auto bg-green-600 text-white rounded-2xl flex items-center justify-center shadow-lg transform -rotate-3">
            <i class="fa fa-map-marker-alt text-4xl"></i>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Portal RT
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Masuk untuk validasi permohonan warga
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-xl sm:rounded-3xl sm:px-10 border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-green-600"></div>
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 text-sm">
                    <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ plugin_route('rt.login.submit') }}" method="POST">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        Username RT
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-user text-gray-400"></i>
                        </div>
                        <input id="username" name="username" type="text" required class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 px-4 py-3 sm:text-sm border-gray-300 bg-gray-50 border rounded-xl" placeholder="Username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 px-4 py-3 sm:text-sm border-gray-300 bg-gray-50 border rounded-xl" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Masuk ke Dasbor
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
