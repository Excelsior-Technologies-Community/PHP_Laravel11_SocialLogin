<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    
    <h1 class="text-xl font-bold text-gray-800">
        Dashboard
    </h1>

    <div class="flex items-center gap-4">
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" alt="Profile" class="w-8 h-8 rounded-full shadow-sm border border-gray-200">
        @endif
        
        <a href="{{ route('logout') }}"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition">
            Logout
        </a>
    </div>

</nav>

<div class="flex-grow flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-xl w-full max-w-lg p-8 text-center border-t-4 border-blue-500">

        <div class="mb-6 flex justify-center">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="User Photo" 
                     class="w-24 h-24 rounded-full border-4 border-blue-100 shadow-md object-cover">
            @else
                <div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center text-white shadow-md">
                    <i class="bi bi-person-fill text-5xl"></i>
                </div>
            @endif
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-2">
            Welcome, {{ Auth::user()->name }} 👋
        </h2>

        <p class="text-gray-500 mb-4">
            {{ Auth::user()->email }}
        </p>

        <hr class="my-4 border-gray-100">

        <p class="text-gray-700 mb-2">
            Login Method:
            <span class="font-semibold text-blue-600 capitalize">
                {{ Auth::user()->provider ?? 'normal' }}
            </span>
        </p>

        <p class="text-gray-700 mb-4">
            Last Login:
            <span class="font-semibold text-green-600">
                {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->format('d M Y, h:i A') : 'First Login' }}
            </span>
        </p>

        <div class="mt-4 flex justify-center gap-2">
            @if(Auth::user()->google_id)
                <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    <i class="bi bi-google me-1"></i> Google User
                </span>
            @elseif(Auth::user()->twitter_id)
                <span class="inline-block bg-sky-100 text-sky-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    <i class="bi bi-twitter me-1"></i> Twitter User
                </span>
            @else
                <span class="inline-block bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    <i class="bi bi-person me-1"></i> Normal User
                </span>
            @endif
        </div>

    </div>

</div>

<footer class="bg-white shadow py-4 text-center text-gray-500 mt-auto">
    © {{ date('Y') }} Laravel Social Login. All rights reserved.
</footer>

</body>
</html>