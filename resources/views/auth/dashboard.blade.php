<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- ============================= -->
<!-- Navbar -->
<!-- ============================= -->
<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    
    <h1 class="text-xl font-bold text-gray-800">
        Dashboard
    </h1>

    <a href="{{ route('logout') }}"
       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition">
        Logout
    </a>

</nav>

<!-- ============================= -->
<!-- Main Content -->
<!-- ============================= -->
<div class="flex-grow flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-xl w-full max-w-lg p-8 text-center">

        <!-- Welcome -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
            Welcome, {{ Auth::user()->name }} 👋
        </h2>

        <!-- Email -->
        <p class="text-gray-500 mb-4">
            {{ Auth::user()->email }}
        </p>

        <!-- Divider -->
        <hr class="my-4">

        <!-- Login Method -->
        <p class="text-gray-700 mb-2">
            Login Method:
            <span class="font-semibold text-blue-600">
                {{ Auth::user()->provider ?? 'normal' }}
            </span>
        </p>

        <!-- Last Login -->
        <p class="text-gray-700 mb-4">
            Last Login:
            <span class="font-semibold text-green-600">
                {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->format('d M Y, h:i A') : 'First Login' }}
            </span>
        </p>

        <!-- Extra Info Badge -->
        <div class="mt-4">
            <span class="inline-block bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded-full">
                {{ Auth::user()->google_id ? 'Google User' : 'Normal User' }}
            </span>
        </div>

    </div>

</div>

<!-- ============================= -->
<!-- Footer -->
<!-- ============================= -->
<footer class="bg-white shadow py-4 text-center text-gray-500">
    © {{ date('Y') }} Laravel Social Login. All rights reserved.
</footer>

</body>
</html>