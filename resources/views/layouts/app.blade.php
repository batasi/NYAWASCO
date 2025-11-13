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

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

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

    <!-- Top Navigation Bar -->
    <div class="nav-top-bar">
        <div class="top-nav-compact">
            <!-- Contact Information -->
            <div class="mobile-contact">
                <a href="tel:+254728725559">
                    <i class="fas fa-phone-alt"></i>
                    <span class="desktop-only">+254 700 00 0000</span>
                </a>
                <a href="mailto:info@nyawasco.co.ke">
                    <i class="fas fa-envelope"></i>
                    <span class="desktop-only">info@nyawasco.co.ke</span>
                </a>
            </div>

            <!-- Social Links -->
            <div class="mobile-social">
                <a href="#" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" aria-label="X">
                    <span style="font-weight: bold; font-size: 1.2em;">X</span>
                </a>
                <a href="#" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" aria-label="Search">
                    <i class="fas fa-search"></i>
                </a>
            </div>
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
                            <a href="{{ route('about') }}">Company Profile</a>
                            <a href="#">Board of Directors</a>
                            <a href="{{ route('management') }}">Management Team</a>
                            <a href="#">Mission & Vision</a>
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
                            <a href="{{ route('payments') }}">Bill Payments</a>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <!-- Customers Dropdown -->
                            <div class="dropdown">
                                <button class="dropdown-btn">
                                    Customers
                                    <i class="fas fa-chevron-down ml-1" style="font-size: 0.75rem;"></i>
                                </button>
                                <div class="dropdown-content">
                                    <a href="{{ route('admin.customers.index') }}">All Customers</a>
                                    <a href="{{ route('water-connection') }}">Register Customer</a>

                                </div>
                            </div>



                            <a href="{{ route('admin.meters.index') }}" class="nav-link">Meters</a>
                            <a href="{{ route('bills.index') }}" class="nav-link">Billings</a>
                            <a href="{{ route('payments.index') }}" class="nav-link">Payments</a>

                        @endif
                    @endauth

                    <a href="{{ route('projects') }}" class="nav-link">Projects</a>
                    <a href="{{ route('careers') }}" class="nav-link">Careers</a>
                </div>

                <!-- Desktop Auth -->
                <div class="nav-auth">
                    @auth
                        <livewire:navigation.user-dropdown />
                    @else
                        <button @click="loginOpen = true" class="btn-secondary">Log in</button>
                        <button @click="signupOpen = true" class="btn-primary">Sign up</button>
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
                    <a href="{{ route('about') }}" @click="mobileMenuOpen = false">Company Profile</a>
                    <a href="#" @click="mobileMenuOpen = false">Board of Directors</a>
                    <a href="{{ route('management') }}" @click="mobileMenuOpen = false">Management Team</a>
                    <a href="#" @click="mobileMenuOpen = false">Mission & Vision</a>
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
                    <a href="{{ route('payments') }}" @click="mobileMenuOpen = false">Bill Payments</a>
                </div>
            </div>

            <a href="{{ route('projects') }}" class="mobile-nav-link" @click="mobileMenuOpen = false">Projects</a>
        </div>

        @guest
        <div class="mobile-auth">
            <button @click="loginOpen = true; mobileMenuOpen = false" class="btn-secondary w-full">Log in</button>
            <button @click="signupOpen = true; mobileMenuOpen = false" class="btn-primary w-full">Sign up</button>
        </div>
        @endguest
    </div>

   <!-- Replace the Sign Up Modal section in your app.blade.php with this: -->
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

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <x-label for="login_email" value="Email Address" />
                        <x-input id="login_email" class="form-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="Enter your email address" />
                    </div>

                    <div class="mb-4">
                        <x-label for="login_password" value="Password" />
                        <x-input id="login_password" class="form-input block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                            Forgot password?
                        </a>
                        @endif
                    </div>

                    <div class="mb-6">
                        <button type="submit" class="w-full btn-primary font-medium py-3 px-4 rounded-lg transition duration-200">
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
                    <a href="{{ route('google.login') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        Sign in with Google
                    </a>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-600">
                        Don't have an account?
                        <button type="button" @click="loginOpen = false; signupOpen = true" class="text-blue-600 hover:text-blue-800 font-medium transition-colors ml-1">
                            Sign up
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-bg">
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
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
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
                            <span>+254 700 00 0000</span>
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
                <p>&copy; 2024 <strong>Nyamira Water and Sanitation Company (NYAWASCO)</strong>. All rights reserved.</p>
            </div>
        </div>
    </footer>

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

    <!-- Livewire Scripts -->
    @livewireScripts
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
