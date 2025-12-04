<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
    <a href="{{ route('logout') }}"
       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition duration-200">
        Logout
    </a>
</nav>

<!-- Main Content -->
<div class="flex-grow flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg w-full max-w-lg p-8 text-center">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Welcome, {{ Auth::user()->name }}!</h2>
        <p class="text-gray-600 mb-6">You are successfully logged in using {{ Auth::user()->google_id ? 'Google' : 'normal credentials' }}.</p>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white shadow py-4 text-center text-gray-500">
    &copy; {{ date('Y') }} Your Company. All rights reserved.
</footer>

</body>
</html>
    