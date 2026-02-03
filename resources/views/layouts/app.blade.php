<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Route;
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NYAWASCO - Nyamira Water and Sanitation Company</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- In your layout head -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Preload critical routes for faster navigation -->
    <link rel="preload" href="{{ route('dashboard') }}" as="document">
    <link rel="preload" href="{{ route('customers.index') }}" as="document">
    <link rel="preload" href="{{ route('meters.index') }}" as="document">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">

    <style>
    [x-cloak] {
        display: none !important;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        background: #FFFFFF;
        color: #000000;
        font-family: 'Arial', sans-serif;
        overflow-x: hidden;
    }

    main {
        min-height: calc(100vh - 200px);
    }

    /* ===== TOP NAVIGATION BAR ===== */
    .nav-top-bar {
        background: #1a3b6a;
        color: #FFFFFF;
        padding: 0.5rem 0;
        font-size: 0.75rem;
    }

    .top-nav-compact {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .mobile-contact {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .mobile-contact a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        text-decoration: none;
        transition: color 0.3s;
        white-space: nowrap;
    }

    .mobile-contact a:hover {
        color: #93c5fd;
    }

    .mobile-social {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .mobile-social a {
        color: white;
        transition: color 0.3s;
        padding: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
    }

    .mobile-social a:hover {
        color: #93c5fd;
        transform: scale(1.1);
    }

    /* Add this to your CSS file or in a style tag */
    .modal-scroll {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .modal-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .modal-scroll::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 3px;
    }

    .modal-scroll::-webkit-scrollbar-thumb {
        background-color: #cbd5e0;
        border-radius: 3px;
    }

    /* Mobile Responsive Top Bar */
    @media (max-width: 640px) {
        .nav-top-bar {
            padding: 0.4rem 0;
        }

        .top-nav-compact {
            justify-content: center;
            gap: 0.75rem;
        }

        .mobile-contact {
            gap: 0.75rem;
            justify-content: center;
        }

        .mobile-contact a span {
            display: none;
        }

        .mobile-contact a {
            gap: 0;
        }

        .mobile-social {
            gap: 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .top-nav-compact {
            flex-direction: column;
            gap: 0.5rem;
        }

        .mobile-contact {
            gap: 1rem;
        }

        .mobile-social {
            gap: 0.75rem;
        }
    }

    @media (max-width: 360px) {
        .mobile-contact {
            gap: 0.75rem;
        }

        .mobile-social {
            gap: 0.5rem;
        }

        .mobile-social a {
            font-size: 0.9rem;
        }
    }

    /* ===== MAIN NAVIGATION ===== */
    .nav-main-bar {
        background: #2567ac;
        color: #FFFFFF;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .nav-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .nav-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 5rem;
    }

    .nav-logo {
        flex-shrink: 0;
    }

    .nav-logo img {
        height: 3.5rem;
        width: auto;
    }

    .nav-links {
        display: none;
    }

    .nav-auth {
        display: none;
    }

    .mobile-menu-btn {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
    }

    .mobile-menu-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Desktop Navigation */
    @media (min-width: 768px) {
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-left: 2.5rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s;
            font-weight: 500;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-auth {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: 1.5rem;
        }

        .mobile-menu-btn {
            display: none;
        }
    }

    /* Dropdown Styles */
    .dropdown {
        position: relative;
    }

    .dropdown-btn {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: none;
        border: none;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 500;
        font-family: inherit;
        font-size: inherit;
    }

    .dropdown-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .dropdown-content {
        position: absolute;
        top: 100%;
        left: 0;
        background: #2567ac;
        min-width: 14rem;
        border-radius: 0.375rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 50;
        border: 1px solid #1a3b6a;
    }

    .dropdown:hover .dropdown-content {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-content a {
        display: block;
        padding: 0.75rem 1rem;
        color: white;
        text-decoration: none;
        transition: background-color 0.3s;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.875rem;
    }

    .dropdown-content a:last-child {
        border-bottom: none;
    }

    .dropdown-content a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* ===== MOBILE MENU ===== */
    .mobile-menu {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: #2567ac;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        overflow-y: auto;
    }

    .mobile-menu.open {
        transform: translateX(0);
    }

    .mobile-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .mobile-menu-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
    }

    .mobile-nav-links {
        padding: 1rem;
    }

    .mobile-nav-item {
        margin-bottom: 0.5rem;
    }

    .mobile-nav-link {
        display: block;
        padding: 0.75rem 1rem;
        color: white;
        text-decoration: none;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
        font-weight: 500;
    }

    .mobile-nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .mobile-dropdown-btn {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        background: none;
        border: none;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 500;
        font-family: inherit;
        font-size: inherit;
    }

    .mobile-dropdown-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .mobile-dropdown-content {
        padding-left: 1rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .mobile-dropdown-content.open {
        max-height: 500px;
    }

    .mobile-dropdown-content a {
        display: block;
        padding: 0.5rem 1rem;
        color: white;
        text-decoration: none;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .mobile-dropdown-content a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .mobile-auth {
        padding: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    /* ===== MODALS ===== */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 32rem;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }

    .modal-header {
        background: #2567ac;
        color: white;
        padding: 1.5rem;
        border-radius: 0.75rem 0.75rem 0 0;
        position: relative;
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.25rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* ===== BUTTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-family: inherit;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .btn-primary {
        background: #2567ac;
        color: white;
    }

    .btn-primary:hover {
        background: #1a3b6a;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: white;
        color: #2567ac;
        border: 1px solid #2567ac;
    }

    .btn-secondary:hover {
        background: #2567ac;
        color: white;
    }

    /* ===== FORM STYLES ===== */
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background: white;
        color: #000000;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        border-color: #2567ac;
        box-shadow: 0 0 0 3px rgba(37, 103, 172, 0.3);
        outline: none;
    }

    /* ===== FOOTER ===== */
    .footer-bg {
        background: #0e1f38;
        color: #FFFFFF;
        padding: 3rem 0 1rem;
    }

    .footer-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .footer-col h4 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .footer-col h4::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 3rem;
        height: 2px;
        background: #3b82f6;
    }

    .footer-links {
        list-style: none;
    }

    .footer-links li {
        margin-bottom: 0.5rem;
    }

    .footer-links a {
        color: #d1d5db;
        text-decoration: none;
        transition: color 0.3s;
        font-size: 0.875rem;
    }

    .footer-links a:hover {
        color: white;
        padding-left: 0.25rem;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Footer Responsive */
    @media (min-width: 768px) {
        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .footer-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* ===== PERFORMANCE OPTIMIZATIONS ===== */
    /* Optimize rendering performance */
    * {
        box-sizing: border-box;
    }

    /* Optimize animations */
    .sidebar-link {
        will-change: opacity, transform;
        backface-visibility: hidden;
        transform: translateZ(0);
    }

    /* ===== SIDEBAR SCROLLBAR STYLING ===== */
    #sidebar::-webkit-scrollbar {
        width: 8px;
    }

    #sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    #sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #16a34a, #15803d);
    }

    #sidebar {
        scrollbar-width: thin;
        scrollbar-color: #22c55e rgba(255, 255, 255, 0.1);
    }

    /* ===== UTILITY CLASSES ===== */
    .text-blue-dark { color: #1a3b6a; }
    .text-blue-light { color: #2567ac; }
    .bg-blue-light { background: #2567ac; }
    .bg-blue-dark { background: #1a3b6a; }
    .border-blue-light { border-color: #2567ac; }

    .emergency-banner {
        background: #e53e3e;
        color: white;
        padding: 0.75rem 0;
        text-align: center;
        font-size: 0.875rem;
    }

    /* ===== RESPONSIVE HELPERS ===== */
    .mobile-only {
        display: block;
    }

    .desktop-only {
        display: none;
    }

    @media (min-width: 768px) {
        .mobile-only {
            display: none;
        }

        .desktop-only {
            display: block;
        }
    }

    /* ===== SWIPER OVERRIDES ===== */
    .hero-swiper {
        width: 100%;
        height: 400px;
    }

    @media (min-width: 768px) {
        .hero-swiper {
            height: 500px;
        }
    }

    @media (min-width: 1024px) {
        .hero-swiper {
            height: 600px;
        }
    }

    /* ===== QUICK LINKS SECTION ===== */
    .quick-links-section {
        background: linear-gradient(135deg, #1a3b6a 0%, #2567ac 50%, #3b82f6 100%);
        position: relative;
        overflow: hidden;
        padding: 3rem 0;
    }

    .quick-links-section::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z' fill='%23ffffff'%3E%3C/path%3E%3C/svg%3E");
        background-size: cover;
        background-position: center;
    }

    .quick-links-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
        position: relative;
        z-index: 1;
    }

    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .quick-link-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        padding: 1.5rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }

    .quick-link-card:hover {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
    }

    .quick-link-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        transition: all 0.3s ease;
    }

    .quick-link-card:hover .quick-link-icon {
        transform: scale(1.1);
    }

    .quick-link-text {
        font-weight: 600;
        color: #1a3b6a;
        font-size: 0.8rem;
        line-height: 1.3;
    }

    /* Quick Links Responsive */
    @media (min-width: 480px) {
        .quick-links-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .quick-link-icon {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }
    }

    @media (min-width: 768px) {
        .quick-links-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .quick-link-card {
            padding: 2rem 1rem;
        }

        .quick-link-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        .quick-link-text {
            font-size: 0.9rem;
        }
    }

    @media (min-width: 1024px) {
        .quick-links-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    }


    /* Add this to your existing CSS in the <style> section */

/* Modal Responsive Improvements */
@media (max-width: 640px) {
    .modal-container {
        margin: 1rem;
        max-height: 85vh;
    }

    .modal-body {
        padding: 1rem;
    }

    .modal-header {
        padding: 1rem;
    }

    .modal-header h2 {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .modal-container {
        margin: 0.5rem;
        border-radius: 0.5rem;
    }

    .grid.grid-cols-1.md\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .modal-body {
        padding: 0.75rem;
    }
}

/* Form input improvements for mobile */
.form-input {
    font-size: 16px; /* Prevents zoom on iOS */
}

/* Ensure proper spacing on very small screens */
@media (max-width: 360px) {
    .modal-body {
        padding: 0.5rem;
    }

    .btn {
        padding: 0.625rem 1.25rem;
        font-size: 0.8rem;
    }
}

/* Loading states for buttons */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 1rem;
    height: 1rem;
    top: 50%;
    left: 50%;
    margin-left: -0.5rem;
    margin-top: -0.5rem;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}


.nav-brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-logo img {
  width: 70px; /* Adjust size as needed */
  height: 70px;
  object-fit: cover;
  border-radius: 50%; /* Makes it circular */
  background-color: #ffffffff;
  padding: 5px;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

    /* Icon color classes remain the same */
    .icon-water { background: linear-gradient(135deg, #e6f2ff 0%, #b3d9ff 100%); color: #0066cc; }
    .icon-sewer { background: linear-gradient(135deg, #e6fff2 0%, #b3ffd9 100%); color: #00a86b; }
    .icon-payment { background: linear-gradient(135deg, #fff8e6 0%, #ffecb3 100%); color: #ff9900; }
    .icon-complaint { background: linear-gradient(135deg, #ffe6e6 0%, #ffb3b3 100%); color: #cc0000; }
    .icon-corruption { background: linear-gradient(135deg, #f0e6ff 0%, #d9b3ff 100%); color: #6633cc; }
    .icon-tenders { background: linear-gradient(135deg, #e6f0ff 0%, #b3d1ff 100%); color: #3366cc; }
    .icon-careers { background: linear-gradient(135deg, #e6fff0 0%, #b3ffd1 100%); color: #00cc66; }
    .icon-reports { background: linear-gradient(135deg, #fff0e6 0%, #ffd1b3 100%); color: #ff6600; }
    .icon-downloads { background: linear-gradient(135deg, #e6f9ff 0%, #b3ecff 100%); color: #0099cc; }
    .icon-news { background: linear-gradient(135deg, #fff0f5 0%, #ffd1e6 100%); color: #cc3366; }
    .icon-contacts { background: linear-gradient(135deg, #f0fff0 0%, #d1ffd1 100%); color: #33cc33; }
    .icon-documentary { background: linear-gradient(135deg, #f0f0ff 0%, #d1d1ff 100%); color: #6666cc; }
</style>

</head>

<body x-data="{
    signupOpen: false,
    loginOpen: false,
    mobileMenuOpen: false,
    mobileDropdowns: {}
}" class="font-sans antialiased body-bg">
    @php
        $showFooter = !auth()->check() || request()->routeIs('home', 'login','board-of-directors','publications','water-supply','sewerage','water-connection',);
    @endphp
    <!-- Top Navigation Bar -->
    @if($showFooter)
    <div class="nav-top-bar" style="background-color:green;">
        <div class="top-nav-compact">
            <!-- Contact Information -->
            <div class="mobile-contact">
                <a href="tel:+254728725559">
                    <i class="fas fa-phone-alt"></i>
                    <span class="desktop-only">+254 787 080 455</span>
                </a>
                <a href="mailto:info@nyawasco.co.ke">
                    <i class="fas fa-envelope"></i>
                    <span class="desktop-only">info@nyawasco.co.ke</span>
                </a>
            </div>

            <!-- Social Links -->

        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="nav-main-bar">
        <div class="nav-container">
            <div class="nav-inner">
                <!-- Logo -->
               <div class="nav-brand">
                    <div class="nav-logo">
                        <a href="{{ route('home') }}">
                        <img src="{{ asset('img/Logo.png') }}" alt="NYAWASCO Logo">
                        </a>
                    </div>

                </div>

                <!-- Desktop Navigation -->
                <div class="nav-links">
                    <a href="{{ route('home') }}" class="nav-link">Home</a>

                    <div class="dropdown">
                        <button class="dropdown-btn">
                            About Us
                            <i class="fas fa-chevron-down ml-1" style="font-size: 0.75rem;"></i>
                        </button>
                        <div class="dropdown-content">
                            <!-- <a href="#">Company Profile</a> -->
                            <a href="{{ route('board-of-directors') }}">Board of Directors</a>
                            <!-- <a href="#">Management Team</a> -->
                            <!-- <a href="#">Mission & Vision</a> -->
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="dropdown-btn">
                            Services
                            <i class="fas fa-chevron-down ml-1" style="font-size: 0.75rem;"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="{{ route('water-supply') }}">Water Supply</a>
                            <a href="{{ route('sewerage') }}">Sewerage Services</a>
                            <a href="{{ route('water-connection') }}">New Water Connection</a>
                            <a href="#">Bill Payments</a>
                        </div>
                    </div>

                    <a href="{{ route('publications') }}" class="nav-link">Publications</a>
                    <a href="#" class="nav-link">Careers</a>
                </div>

                <!-- Desktop Auth -->
                <div class="nav-auth">
                    @auth
                        <livewire:navigation.user-dropdown />
                    @else
                        <button @click="loginOpen = true" class="btn-primary">Log in</button>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = true" class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
        <div class="mobile-menu-header">
            <div class="nav-logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/Logo.png') }}" alt="NYAWASCO Logo" class="h-10">
                </a>
            </div>
            <button @click="mobileMenuOpen = false" class="mobile-menu-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mobile-nav-links">
            <a href="{{ route('home') }}" class="mobile-nav-link" @click="mobileMenuOpen = false">Home</a>

            <div class="mobile-nav-item">
                <button class="mobile-dropdown-btn" @click="mobileDropdowns.about = !mobileDropdowns.about">
                    About Us
                    <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': mobileDropdowns.about }"></i>
                </button>
                <div class="mobile-dropdown-content" :class="{ 'open': mobileDropdowns.about }">
                    <!-- <a href="{{ route('about') }}" @click="mobileMenuOpen = false">Company Profile</a> -->
                    <a href="{{ route('board-of-directors') }}" @click="mobileMenuOpen = false">Board of Directors</a>
                    <!-- <a href="{{ route('management') }}" @click="mobileMenuOpen = false">Management Team</a> -->
                    <!-- <a href="#" @click="mobileMenuOpen = false">Mission & Vision</a> -->
                </div>
            </div>

            <div class="mobile-nav-item">
                <button class="mobile-dropdown-btn" @click="mobileDropdowns.services = !mobileDropdowns.services">
                    Services
                    <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': mobileDropdowns.services }"></i>
                </button>
                <div class="mobile-dropdown-content" :class="{ 'open': mobileDropdowns.services }">
                    <a href="{{ route('water-supply') }}" @click="mobileMenuOpen = false">Water Supply</a>
                    <a href="{{ route('sewerage') }}" @click="mobileMenuOpen = false">Sewerage Services</a>
                    <a href="{{ route('water-connection') }}" @click="mobileMenuOpen = false">New Water Connections</a>
                    <a href="#" @click="mobileMenuOpen = false">Bill Payments</a>
                </div>
            </div>

            <a href="{{ route('publications') }}" class="mobile-nav-link" @click="mobileMenuOpen = false">Publications</a>
        </div>

        <!-- Replace the existing mobile-auth section with this: -->
        @guest
        <div class="mobile-auth">
            <button @click="loginOpen = true; mobileMenuOpen = false" class="btn-primary w-full">Log in</button>
        </div>
        @else
        <!-- Add this for logged-in users in mobile menu -->
        <div class="mobile-auth">
            <div class="mb-4">
                <div class="flex items-center space-x-3 p-2 bg-blue-900/30 rounded-lg">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                            class="h-8 w-8 rounded-full object-cover border border-white/50">
                    @else
                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-green-500 flex items-center justify-center">
                            <span class="text-white text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-blue-200">Administrator</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('dashboard') }}"
            @click="mobileMenuOpen = false"
            class="block w-full text-center py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors mb-2">
                Dashboard
            </a>

            <a href="{{ route('profile.edit') }}"
            @click="mobileMenuOpen = false"
            class="block w-full text-center py-2 bg-blue-900/50 hover:bg-blue-800/70 text-white rounded-lg transition-colors mb-2">
                Profile Settings
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                        @click="mobileMenuOpen = false"
                        class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Logout
                </button>
            </form>
        </div>
        @endguest
    </div>
    @endif
    <!-- Sign Up Modal -->
    <div x-show="signupOpen" x-cloak class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="text-2xl font-bold">Create Your Account</h2>
                <button @click="signupOpen = false" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-6 justify-center flex">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto">
                    </a>
                </div>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}" id="registrationForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="name" value="Full Name *" />
                            <x-input id="name" type="text" name="name" :value="old('name')" required autofocus class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="email" value="Email Address *" />
                            <x-input id="email" type="email" name="email" :value="old('email')" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="phone" value="Phone Number *" />
                            <x-input id="phone" type="tel" name="phone" :value="old('phone')" required class="form-input w-full mt-1" placeholder="+254 XXX XXX XXX" />
                        </div>
                        <div>
                            <x-label for="id_number" value="ID Number *" />
                            <x-input id="id_number" type="text" name="id_number" :value="old('id_number')" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="password" value="Password *" />
                            <x-input id="password" type="password" name="password" required autocomplete="new-password" class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="password_confirmation" value="Confirm Password *" />
                            <x-input id="password_confirmation" type="password" name="password_confirmation" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-label for="address" value="Physical Address *" />
                        <textarea id="address" name="address" required class="form-input w-full mt-1" rows="3" placeholder="Enter your complete physical address">{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="terms" required class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="{{ route('terms') }}" class="text-blue-600 hover:text-blue-800">Terms of Service</a> and <a href="{{ route('privacy') }}" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full btn-primary font-medium py-3 px-4 rounded-lg transition duration-200">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-600">
                        Already have an account?
                        <button type="button" @click="signupOpen = false; loginOpen = true" class="text-blue-600 hover:text-blue-800 font-medium transition-colors ml-1">
                            Log in
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Replace the Login Modal section in your app.blade.php with this: -->
    <!-- Login Modal -->
    <div x-show="loginOpen" x-cloak class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="text-2xl font-bold">Welcome Back</h2>
                <button @click="loginOpen = false" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="mb-6 justify-center flex">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto">
                    </a>
                </div>

                <form method="POST" action="{{ route('login') }}" autocomplete="off">
                    @csrf

                    <!-- Email OR Username -->
                    <div class="mb-4">
                        <x-label for="login" value="Email or Username" />
                        <x-input
                            id="login"
                            class="form-input block mt-1 w-full"
                            type="text"
                            name="login"
                            value="{{ old('login') }}"
                            required
                            autofocus
                            autocomplete="off"
                            placeholder="Enter email or username"
                        />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <x-label for="password" value="Password" />
                        <x-input
                            id="password"
                            class="form-input block mt-1 w-full"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        />
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="remember"
                                class="rounded border-gray-300 text-blue-600 shadow-sm
                                    focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <div class="mb-6">
                        <button type="submit"
                                class="w-full btn-primary font-medium py-3 px-4 rounded-lg transition duration-200">
                            Log In
                        </button>
                    </div>
                </form>

                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('google.login') }}"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border
                            border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium
                            text-gray-700 hover:bg-gray-50 transition duration-200">
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        Sign in with Google
                    </a>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-600">
                        Don't have an account?
                        <button type="button"
                                @click="loginOpen = false; signupOpen = true"
                                class="text-blue-600 hover:text-blue-800 font-medium transition-colors ml-1">
                            Sign up
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>


    <!-- Page Content -->
     @if(!$showFooter)

        <!-- Sidebar Layout for Authenticated Users -->
        <div class="flex h-screen bg-gradient-to-br from-slate-50 to-blue-50">
            <!-- Sidebar -->
            <div id="sidebar" class="bg-gradient-to-b from-blue-200 via-blue-300 to-blue-400 text-gray-800 w-64 md:w-72 space-y-6 md:space-y-8 py-6 md:py-8 px-4 md:px-6 absolute left-0 top-0 transform -translate-x-full md:relative md:translate-x-0 transition-all duration-300 ease-in-out z-50 shadow-2xl min-h-screen overflow-y-auto">
                <!-- Logo Section -->
                <div class="relative flex flex-col items-center justify-center mb-8">
                    <button onclick="toggleSidebar()" class="absolute top-0 right-0 text-white hover:text-gray-300 p-2 md:hidden">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="flex items-center justify-center mb-3">
                        <div class="bg-white p-2 rounded-2xl shadow-lg">
                            <img src="{{ asset('img/Logo.png') }}" alt="NYAWASCO Logo" class="w-8 h-8 md:w-12 md:h-12 rounded-xl object-cover">
                        </div>
                    </div>
                    <span class="text-sm md:text-lg font-bold text-gray-800">Nyamira Water</span>
                    <p class="text-xs md:text-sm text-blue-600 font-medium">and Sanitation Company</p>
                    <div class="w-20 h-1 bg-gradient-to-r from-green-400 to-blue-500 rounded-full mt-3"></div>
                </div>

                <!-- Menu -->
                <nav class="space-y-2">
                    <div class="px-3 md:px-4 py-2">
                        <h3 class="text-xs md:text-sm font-bold text-gray-700 uppercase tracking-wider">Bills & Customers</h3>
                    </div>
                    <a href="{{ route('dashboard') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('dashboard') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="dashboard">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    @can('view customers')
                     <a href="{{ route('customers.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('customers.*') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="customers">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('customers.*') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="font-medium">Customers</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>

                    @endcan
                    @can('view meters')
                    <a href="{{ route('meters.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('meters.*') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="meters">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('meters.*') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        <span class="font-medium">Meters</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    @endcan
                    @can('view readings')
                    <a href="{{ route('meter-readings.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('meter-readings.*') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="meters">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('meter-readings.*') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        <span class="font-medium">Meter Readings</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    @endcan
                    @can('view bills')
                    <a href="{{ route('bills.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('bills.*') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="bills">
                       <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('bills.*') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                       </svg>
                       <span class="font-medium">Bills</span>
                       <div class="sidebar-loading ml-auto hidden">
                           <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                               <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                               <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                           </svg>
                       </div>
                   </a>
                    @endcan
                    @can('view payments')
                    <a href="{{ route('payments.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('payments.*') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="payments">
                       <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('payments.*') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                       </svg>
                       <span class="font-medium">Payments</span>
                       <div class="sidebar-loading ml-auto hidden">
                           <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                               <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                               <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                           </svg>
                       </div>
                   </a>
                    @endcan
                    @can('view reports')
                    <a href="{{ route('reports.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('reports.*') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="reports">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span class="font-medium">Reports</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    @endcan
                    @can('view payments')
                    <div class="px-3 md:px-4 py-2">
                        <h3 class="text-xs md:text-sm font-bold text-gray-700 uppercase tracking-wider">FINANCIAL MANAGEMENT</h3>
                    </div>

                    <a href="#" onclick="toggleSubmenu('accounts-receivable')" class="sidebar-link group flex items-center justify-between py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-medium">Accounts Receivable</span>
                        </div>
                        <svg id="accounts-receivable-arrow" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>

                    {{-- Accounts Receivable Submenu --}}
                    <ul id="accounts-receivable-submenu" class="ml-8 mt-1 space-y-1 hidden">
                        <li>
                            <a href="{{ route('admin.accounts-receivable.dashboard') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.accounts-receivable.dashboard') ? 'bg-blue-200/50' : '' }}" data-route="accounts-receivable.dashboard">
                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.accounts-receivable.aging-report') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.accounts-receivable.aging-report') ? 'bg-blue-200/50' : '' }}" data-route="accounts-receivable.aging-report">
                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Aging Report
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.accounts-receivable.collections-tracking') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.accounts-receivable.collections-tracking') ? 'bg-blue-200/50' : '' }}" data-route="accounts-receivable.collections-tracking">
                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Collections Tracking
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.accounts-receivable.write-offs.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.accounts-receivable.write-offs.*') ? 'bg-blue-200/50' : '' }}" data-route="accounts-receivable.write-offs">
                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Write-offs
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customers.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.accounts-receivable.customer-balances') ? 'bg-blue-200/50' : '' }}" data-route="accounts-receivable.customer-balances">
                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Customer Balances
                            </a>
                        </li>
                    </ul>



                    <a href="#" onclick="toggleSubmenu('payments')" class="sidebar-link group flex items-center justify-between py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-medium">Accounting & General Ledger</span>
                        </div>
                        <svg id="payments-arrow" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                     <a href="#"  class="sidebar-link group flex items-center justify-between py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-medium">Bank Reconciliation</span>
                        </div>

                    </a>

                    {{-- Payments Submenu --}}
                    <ul id="payments-submenu" class="ml-8 mt-1 space-y-1 hidden">
                        <li>
                            <a href="{{ route('admin.payments.dashboard') }}"
                            onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()"
                            class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.payments.dashboard') ? 'bg-blue-200/50' : '' }}"
                            data-route="admin.payments.dashboard">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Payments Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.payments.unallocated') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.payments.unallocated') ? 'bg-blue-200/50' : '' }}" data-route="payments.unallocated">
                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Unallocated Payments
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.payments.methods-report') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 px-4 rounded-lg transition-all duration-200 hover:bg-blue-200/30 text-sm {{ request()->routeIs('admin.payments.methods-report') ? 'bg-blue-200/50' : '' }}" data-route="payments.methods-report">
                                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Payment Methods Report
                            </a>
                        </li>
                    </ul>
                    @endcan
                    @can('view users')
                    <div class="px-3 md:px-4 py-2">
                        <h3 class="text-xs md:text-sm font-bold text-gray-700 uppercase tracking-wider">System Administration</h3>
                    </div>
                    <a href="{{ route('system.management') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('system.management') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="system-management">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('system.management') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="font-medium">System Management</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    <a href="{{ route('admin.users.index') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('system.user.management') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="user-management">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('system.user.management') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <span class="font-medium">User Management</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    <a href="{{ route('system.sessions.logs') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('system.sessions.logs') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="sessions-logs">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('system.sessions.logs') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-medium">Sessions & Logs</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    <a href="{{ route('system.analysis') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('system.analysis') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="system-analysis">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('system.analysis') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="font-medium">System Analysis</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    <a href="{{ route('system.backups') }}" onclick="handleSidebarLink(this); if (window.innerWidth < 768) toggleSidebar()" class="sidebar-link group flex items-center py-2 md:py-3 px-3 md:px-4 rounded-xl transition-all duration-200 hover:bg-blue-300/30 hover:shadow-lg {{ request()->routeIs('system.backups') ? 'bg-blue-300/50 shadow-lg border-l-4 border-green-600' : '' }}" data-route="system-backups">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-3 {{ request()->routeIs('system.backups') ? 'text-green-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <span class="font-medium">System Backups</span>
                        <div class="sidebar-loading ml-auto hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </a>
                    @endcan
                </nav>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-40 md:hidden hidden" onclick="toggleSidebar()"></div>

            <!-- Main content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="bg-white/90 backdrop-blur-xl shadow-2xl border-b border-gray-200/30 px-6 md:px-8 py-4 md:py-6 relative overflow-hidden">
                    <!-- Background gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-50/50 via-white/50 to-green-50/50"></div>

                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Modernized Sidebar Toggle -->
                            <button onclick="toggleSidebar()" class="group relative p-3 rounded-xl text-gray-600 hover:text-white hover:bg-gradient-to-r hover:from-blue-500 hover:to-green-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 mr-2">
                                <svg class="w-6 h-6 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-green-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </button>

                            <!-- Company Branding -->
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-white p-1 shadow-lg border border-gray-200/50">
                                    <img src="{{ asset('img/Logo.png') }}" alt="NYAWASCO Logo" class="w-full h-full object-contain rounded-lg">
                                </div>
                                <div>
                                    <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Nyamira Water</h1>
                                    <p class="text-xs md:text-sm text-blue-500 font-medium">and Sanitation Company</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Modernized User Profile -->
                            <a href="{{ route('profile.edit') }}" class="group flex items-center space-x-3 bg-white/70 backdrop-blur-sm rounded-xl px-4 py-3 hover:bg-white/90 transition-all duration-300 shadow-md hover:shadow-xl border border-gray-200/50 hover:border-blue-300/50 transform hover:scale-105">
                                <div class="relative">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-lg ring-2 ring-blue-100">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 via-blue-500 to-green-500 flex items-center justify-center border-2 border-white shadow-lg ring-2 ring-blue-100">
                                            <span class="text-white font-bold text-lg">
                                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white shadow-sm"></div>
                                </div>
                                <div class="hidden md:block">
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">{{ auth()->user()->name ?? 'User' }}</p>
                                    <p class="text-xs text-gray-500">Administrator</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>

                            <!-- Modernized Logout Button -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="group relative bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-105 hover:-translate-y-1 overflow-hidden">
                                    <span class="relative z-10 flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span>Logout</span>
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-red-400 to-red-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-slate-50 to-blue-50 p-2.5">
                    @yield('content')
                </main>
            </div>
        </div>

        <script>
            function toggleSubmenu(menuId) {
    const submenu = document.getElementById(`${menuId}-submenu`);
    const arrow = document.getElementById(`${menuId}-arrow`);

    if (submenu && arrow) {
        submenu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }
}

// Keep submenus open based on current route
document.addEventListener('DOMContentLoaded', function() {
    // Check which submenu should be open
    const currentPath = window.location.pathname;

    // Accounts Receivable submenu
    if (currentPath.includes('accounts-receivable')) {
        toggleSubmenu('accounts-receivable');
    }

    // Payments submenu
    if (currentPath.includes('payments') &&
        (currentPath.includes('unallocated') || currentPath.includes('methods-report'))) {
        toggleSubmenu('payments');
    }
});
        // Global variable to track loading state
        let isNavigating = false;

        function handleSidebarLink(element) {
            if (isNavigating) {
                return false; // Prevent multiple clicks
            }

            isNavigating = true;

            // Show loading spinner
            const loadingElement = element.querySelector('.sidebar-loading');
            if (loadingElement) {
                loadingElement.classList.remove('hidden');
            }

            // Add visual feedback
            element.classList.add('pointer-events-none', 'opacity-75');

            // Disable all other sidebar links temporarily
            document.querySelectorAll('.sidebar-link').forEach(link => {
                if (link !== element) {
                    link.classList.add('pointer-events-none', 'opacity-50');
                }
            });

            // Set a timeout to reset if navigation takes too long (fallback)
            setTimeout(() => {
                if (isNavigating) {
                    resetSidebarState();
                }
            }, 10000); // 10 second timeout

            return true;
        }

        function resetSidebarState() {
            isNavigating = false;

            // Hide all loading spinners
            document.querySelectorAll('.sidebar-loading').forEach(el => {
                el.classList.add('hidden');
            });

            // Re-enable all sidebar links
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('pointer-events-none', 'opacity-75', 'opacity-50');
            });
        }

        // Reset state when page becomes visible (user navigated back)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                resetSidebarState();
            }
        });

        // Reset state on page load
        window.addEventListener('load', function() {
            resetSidebarState();
        });

        // Performance optimizations
        // Debounce function for better performance
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Optimize scroll performance
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    // Handle scroll-based optimizations here
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Preload critical resources
        function preloadCriticalResources() {
            // Preload critical images
            const criticalImages = [
                '{{ asset("img/Logo.png") }}',
                '{{ asset("img/favicon-32x32.png") }}'
            ];

            criticalImages.forEach(src => {
                const img = new Image();
                img.src = src;
            });
        }

        // Initialize performance optimizations
        document.addEventListener('DOMContentLoaded', function() {
            preloadCriticalResources();

            // Add passive listeners for better scroll performance
            document.addEventListener('touchstart', function() {}, { passive: true });
            document.addEventListener('touchmove', function() {}, { passive: true });
        });

        // Optimized toggleSidebar function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            // Use requestAnimationFrame for smoother animations
            requestAnimationFrame(() => {
                if (window.innerWidth >= 768) {
                    // Desktop toggle
                    const isVisible = sidebar.classList.contains('md:translate-x-0');
                    if (isVisible) {
                        // Hide sidebar
                        sidebar.classList.remove('md:translate-x-0', 'md:relative');
                        sidebar.classList.add('md:-translate-x-full');
                    } else {
                        // Show sidebar
                        sidebar.classList.remove('md:-translate-x-full');
                        sidebar.classList.add('md:translate-x-0', 'md:relative');
                    }
                } else {
                    // Mobile toggle
                    const isOpen = sidebar.classList.contains('translate-x-0');
                    if (isOpen) {
                        // Close mobile sidebar
                        sidebar.classList.remove('translate-x-0');
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    } else {
                        // Open mobile sidebar
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        overlay.classList.remove('hidden');
                    }
                }
            });
        }

        // Reports menu toggle
        document.getElementById('reports-toggle').addEventListener('click', function() {
            const submenu = document.getElementById('reports-submenu');
            submenu.classList.toggle('hidden');
            document.getElementById('reports-arrow').classList.toggle('rotate-90');
        });

        // Revenue Reports toggle
        document.getElementById('revenue-toggle').addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering parent
            const submenu = document.getElementById('revenue-submenu');
            submenu.classList.toggle('hidden');
            document.getElementById('revenue-arrow').classList.toggle('rotate-90');
        });

        // Performance Reports toggle
        document.getElementById('performance-toggle').addEventListener('click', function(e) {
            e.stopPropagation();
            const submenu = document.getElementById('performance-submenu');
            submenu.classList.toggle('hidden');
            document.getElementById('performance-arrow').classList.toggle('rotate-90');
        });


        </script>
    @else
        <!-- Original Layout for Non-Authenticated Users -->
        <main>
            @yield('content')
        </main>
    @endauth



    @if($showFooter)
    <footer class="footer-bg" style="background-color:#2567ac;">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div>
                    <a href="{{ route('home') }}" class="flex items-center mb-4">
                        <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto">
                    </a>
                    <p class="text-gray-300 text-sm leading-relaxed mb-4">
                        Providing reliable water and sanitation services to our community with commitment and excellence.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors" aria-label="X">
                            <span style="font-weight: bold; font-size: 1.2em;">X</span>
                        </a>

                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('services') }}">Services</a></li>
                        <li><a href="{{ route('projects') }}">Projects</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4>Our Services</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('water-supply') }}">Water Supply</a></li>
                        <li><a href="{{ route('sewerage') }}">Sewerage Services</a></li>
                        <li><a href="{{ route('new-connections') }}">New Connections</a></li>
                        <li><a href="{{ route('payments') }}">Bill Payments</a></li>
                        <li><a href="{{ route('support') }}">Customer Support</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4>Contact Us</h4>
                    <ul class="footer-links">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-blue-400"></i>
                            <span>NYAWASCO Headquarters<br>Nyamira, Kenya</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-blue-400"></i>
                            <span>+254 787 080 455</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <span>info@nyawasco.co.ke</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-blue-400"></i>
                            <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p>&copy; {{ now()->year }} <strong>Nyamira Water and Sanitation Company. <br>
                <span>  Powered by Quantum Inka Technologies</span></strong>. <br> All rights reserved.</p>
            </div>
        </div>
    </footer>
    @endif

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper
            const heroSwiper = new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const mobileMenu = document.querySelector('.mobile-menu');
                const mobileMenuBtn = document.querySelector('.mobile-menu-btn');

                if (mobileMenu.classList.contains('open') &&
                    !mobileMenu.contains(event.target) &&
                    !mobileMenuBtn.contains(event.target)) {
                    mobileMenu.classList.remove('open');
                }
            });

            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                const signupModal = document.querySelector('[x-show="signupOpen"]');
                const loginModal = document.querySelector('[x-show="loginOpen"]');

                if (signupModal && signupModal.style.display !== 'none' &&
                    !signupModal.querySelector('.modal-container').contains(event.target)) {
                    signupOpen = false;
                }

                if (loginModal && loginModal.style.display !== 'none' &&
                    !loginModal.querySelector('.modal-container').contains(event.target)) {
                    loginOpen = false;
                }
            });
        });

        // Close mobile menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const mobileMenu = document.querySelector('.mobile-menu');
                if (mobileMenu.classList.contains('open')) {
                    mobileMenu.classList.remove('open');
                }
            }
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
    <script src="{{ asset('js/services/OCRService.js') }}"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Before closing body -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Livewire Scripts -->
    @livewireScripts
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
