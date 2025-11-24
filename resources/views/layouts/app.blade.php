<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="/snackszone-logo.ico">

    <title>{{ config('app.name', 'Snacks Zone Admin') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Navbar height control - change this if you change navbar sizing */
        :root {
            --navbar-height: 72px; /* adjust if needed */
            --navbar-top-gap: 10px; /* gap from top of viewport */
        }

        /* REMOVE TOP EMPTY SPACE - body padding set relative to navbar height */
        body {
            margin: 0;
            padding-top: calc(var(--navbar-height) + var(--navbar-top-gap));
            font-family: Arial, sans-serif;
            background-color: #ffd54f; /* original .jack background */
        }

        /* NAVBAR */
        .navbar-main {
            --internal-padding: 12px 18px;
            position: fixed;
            top: var(--navbar-top-gap);
            left: 50%;
            transform: translateX(-50%);
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            width: calc(100% - 40px); /* leave small side margin on very small screens */
            max-width: 1200px;
            background: #1E3A8A;
            border-bottom: 3px solid #15306c;
            border-radius: 40px;
            padding: var(--internal-padding);
            z-index: 9999; /* ensure above content */
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }

        .navbar-main img {
            border: 2px solid #fff;
            border-radius: 8px;
            height: 44px;
            width: 44px;
            object-fit: cover;
            background: #fff;
        }

        /* nav list styles - horizontal by default */
        .navbar-nav-inline {
            display: flex;
            gap: 10px;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .navbar-nav-inline li a {
            color: #fff;
            text-decoration: none;
            padding: 6px 8px;
            display: inline-block;
        }

        .navbar-nav-inline li a:hover {
            color: #ffd54f;
            padding-left: 6px;
        }

        .nav-active {
            color: #FFD54F !important;
            font-weight: bold;
        }

        /* Logout button style */
        .logout-btn {
            background: #FFD54F;
            color: #000;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        /* Page content area */
        #content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 40px; /* bottom padding for spacing */
            
        }

        /* Simple header card style */
        header.bg-white {
            border-radius: 8px;
        }

        /* Toggle button hidden on desktop */
        .mobile-toggle-btn {
            display: none;
            font-size: 28px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

        /* MOBILE MENU (closed by default) */
        #mobileMenu {
            display: flex;
            transition: max-height .3s ease;
        }

        /* Mobile Responsive */
        @media (max-width: 720px) {

            .mobile-toggle-btn {
                display: block;
                margin-left: auto;
                margin-right: 10px;
            }

            .desktop-spacer {
                display: none;
            }

            .navbar-nav-inline {
                position: absolute;
                top: calc(var(--navbar-height) + var(--navbar-top-gap));
                left: 0;
                width: 100%;
                background: #1E3A8A;
                flex-direction: column;
                border-radius: 12px;
                overflow: hidden;
                max-height: 0;
                transition: max-height .3s ease;
                z-index: 99999;
            }

            .navbar-nav-inline.show {
                max-height: 450px;
                padding-bottom: 10px;
            }

            .navbar-nav-inline li a {
                display: block;
                padding: 12px 14px;
                border-bottom: 1px solid rgba(255,255,255,0.15);
                width: 100%;
            }
        }

        
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar-main">

        <!-- Left Side: Logo + Title -->
        <div style="display:flex; align-items:center; gap:12px;">
            <img src="{{ asset('images/snackszone-logo.png') }}" alt="logo">
            <span class="text-white" style="font-size:1.125rem; font-weight:700;">
                Snacks Zone Admin
            </span>
        </div>

        <!-- MOBILE TOGGLE BUTTON -->
        <button class="mobile-toggle-btn" onclick="toggleMobileMenu()">
            ☰
        </button>

        <!-- SPACER (desktop only) -->
        <div class="desktop-spacer"></div>

        <!-- NAV MENU -->
        <ul class="navbar-nav-inline" id="mobileMenu">
            <li><a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                    📊 Dashboard
            </a></li>

            <li><a href="{{ route('products.index') }}"
                class="{{ request()->routeIs('products.*') ? 'nav-active' : '' }}">
                    📦 Products
            </a></li>

            <li><a href="{{ route('stock.index') }}"
                class="{{ request()->routeIs('stock.*') ? 'nav-active' : '' }}">
                    📥 Stock
            </a></li>

            <li>
                <a href="{{ route('pos.create') }}"
                class="{{ request()->routeIs('pos.*') ? 'nav-active' : '' }}">
                🧾 POS
                </a>
            </li>

            <li>
                <a href="{{ route('invoices.index') }}"
                class="{{ request()->routeIs('invoice.*') ? 'nav-active' : '' }}">
                🧻 Invoices
                </a>
            </li>


            <li><a href="{{ route('reports.index') }}"
                class="{{ request()->routeIs('reports.*') ? 'nav-active' : '' }}">
                    📑 Reports
            </a></li>

            <li><a href="{{ route('expenses.index') }}"
                class="{{ request()->routeIs('expenses.*') ? 'nav-active' : '' }}">
                    💸 Expenses
            </a></li>

            <li><a href="{{ route('about') }}"
                class="{{ request()->routeIs('about') ? 'nav-active' : '' }}">
                    🏢 About Company
            </a></li>

            <li>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button class="logout-btn" type="submit">Logout</button>
                </form>
            </li>
        </ul>

    </nav>

    <!-- PAGE CONTENT -->
    <div id="content" class="mt-4">
        {{ $slot ?? $content ?? '' }}

        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow my-4 p-4 rounded">
                {{ $header }}
            </header>
        @endisset

        @yield('content')
        @yield('scripts')
    </div>
    <script>
        function toggleMobileMenu() {
            document.getElementById("mobileMenu").classList.toggle("show");
        }
    </script>
</body>
</html>
