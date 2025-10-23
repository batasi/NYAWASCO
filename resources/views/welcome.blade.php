<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Javent - Events & Voting Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        :root {
            --primary: #ec4899;
            --primary-dark: #db2777;
            --primary-light: #fce7f3;
            --secondary: #000000;
            --secondary-dark: #000000;
            --secondary-light: #333333;
            --accent: #be185d;
            --success: #059669;
            --warning: #d97706;
            --info: #0891b2;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            max-width: 100%;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #000000;
            min-height: 100vh;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            font-size: 14px;
            color: #ffffff;
        }

        /* Enhanced Mobile Navigation - Pink Theme */
        .mobile-nav {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(236, 72, 153, 0.3);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            padding: 0.75rem 1rem;
            z-index: 1000;
            height: 64px;
            align-items: center;
            justify-content: space-between;
        }
        
        .mobile-nav-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        
        .mobile-menu-toggle {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            z-index: 1001;
        }

        .mobile-menu-toggle span {
            width: 20px;
            height: 2px;
            background: var(--primary);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
            background: var(--primary);
        }

        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
            background: var(--primary);
        }

        .mobile-menu {
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.98);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(236, 72, 153, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 1rem;
            transform: translateY(-100%);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .mobile-menu.active {
            transform: translateY(0);
            opacity: 1;
        }

        .mobile-menu-link {
            display: block;
            padding: 1rem;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1rem;
            border: 1px solid transparent;
        }

        .mobile-menu-link:hover {
            background: rgba(236, 72, 153, 0.1);
            color: var(--primary);
            border-color: rgba(236, 72, 153, 0.3);
        }

        .mobile-nav-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .mobile-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            min-height: 40px;
        }
        
        .mobile-btn-outline {
            background: transparent;
            color: #ffffff;
            border: 1.5px solid var(--primary);
        }
        
        .mobile-btn-outline:hover {
            background: var(--primary);
            color: #000000;
        }
        
        .mobile-btn-primary {
            background: var(--primary);
            color: #000000;
            border: 1.5px solid var(--primary);
            font-weight: 700;
        }
        
        .mobile-btn-primary:hover {
            background: #ffffff;
            border-color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.4);
        }

        /* Desktop Navigation - Pink/Black Theme */
        .nav-blur {
            display: none;
        }
        
        /* Main Content - Full Width */
        .main-content {
            width: 100%;
            padding-top: 64px;
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }

        /* Full Width Sections */
        .full-width-section {
            width: 100%;
            padding: 2rem 0;
        }

        .section-inner {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Professional Slideshow - Full Width */
        .slideshow-container {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            margin: 0;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .slide.active {
            opacity: 1;
        }

        .slide-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.9));
            color: white;
            padding: 2rem;
            z-index: 2;
        }

        .slide-badge {
            background: var(--primary);
            color: #000000;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .slide-content h2 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            line-height: 1.2;
            color: #ffffff;
        }

        .slide-content p {
            font-size: 1rem;
            opacity: 0.9;
            max-width: 100%;
            margin-bottom: 0;
            line-height: 1.5;
            color: #ffffff;
        }

        .slide-indicators {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
            z-index: 10;
        }

        .slide-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .slide-indicator.active {
            background: var(--primary);
            transform: scale(1.2);
        }

        /* Content Sections - Full Width with Pink/Black Theme */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .content-section {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(236, 72, 153, 0.1);
            border: 1px solid rgba(236, 72, 153, 0.2);
            transition: all 0.3s ease;
        }
        
        .content-section:hover {
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.2);
            transform: translateY(-2px);
            border-color: rgba(236, 72, 153, 0.4);
        }
        
        .section-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(236, 72, 153, 0.3);
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }
        
        .view-all {
            color: var(--primary);
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background: rgba(236, 72, 153, 0.1);
            text-decoration: none;
            border: 1px solid transparent;
        }
        
        .view-all:hover {
            background: var(--primary);
            color: #000000;
            border-color: var(--primary);
        }
        
        .content-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .content-item:hover {
            background: rgba(236, 72, 153, 0.1);
            border-color: rgba(236, 72, 153, 0.3);
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            margin-bottom: 1rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .voting .item-image {
            background: linear-gradient(135deg, #000000, var(--primary));
            color: #ffffff;
        }
        
        .item-details {
            width: 100%;
        }
        
        .item-title {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
            font-size: 1rem;
            line-height: 1.4;
        }
        
        .item-meta {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .item-badge {
            background: var(--primary);
            color: #000000;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        
        .voting .item-badge {
            background: #000000;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        /* Full Width Content Sections */
        .full-width-section .content-section {
            width: 100%;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(236, 72, 153, 0.1);
            border: 1px solid rgba(236, 72, 153, 0.2);
        }

        .full-width-section .content-section .content-slideshow {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Tablet and Desktop Styles */
        @media (min-width: 768px) {
            .full-width-section .content-section {
                max-width: 1000px;
                padding: 2.5rem;
            }
            
            .full-width-section .content-section .content-slideshow {
                height: 320px;
            }
        }

        /* Small Mobile Optimization */
        @media (max-width: 360px) {
            .full-width-section {
                padding: 1rem 0;
            }
            
            .full-width-section .content-section {
                padding: 1.5rem;
                margin: 0 0.5rem;
            }
            
            .full-width-section .content-section .content-slideshow {
                height: 250px;
            }
        }

        /* Enhanced Content Slideshow with Image Backgrounds */
        .content-slide {
            cursor: pointer;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
        }

        .content-slide .slide-content-wrapper {
            pointer-events: none;
        }

        .content-slide .slide-badge-small {
            pointer-events: auto;
        }

        .content-slide.active {
            opacity: 1;
        }

        /* Fallback gradients when no image is available */
        .content-slide.events {
            background: linear-gradient(135deg, rgba(236,72,153,0.9), rgba(0,0,0,0.8)) !important;
        }

        .content-slide.voting {
            background: linear-gradient(135deg, rgba(0,0,0,0.9), rgba(236,72,153,0.8)) !important;
        }

        /* Ensure content is readable over background images */
        .slide-content-wrapper {
            color: white;
            text-align: center;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .slide-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.3;
            text-shadow: 0 2px 4px rgba(0,0,0,0.7);
        }

        .slide-meta {
            font-size: 0.9rem;
            opacity: 0.95;
            margin-bottom: 1rem;
            line-height: 1.4;
            text-shadow: 0 2px 4px rgba(0,0,0,0.7);
        }

        /* Enhanced badges for better visibility */
        .slide-badge-small {
            background: var(--primary);
            color: #000000;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .slide-badge-small:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
            background: #ffffff;
            color: #000000;
        }

        .voting .slide-badge-small {
            background: #000000;
            color: var(--primary);
            border: 1px solid var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }


        .voting .slide-badge-small:hover {
            background: var(--primary);
            color: #000000;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
        }

        /* Enhanced slide icon for better visibility */
        .slide-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* Content Section Slideshows */
        .content-slideshow {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 12px;
        }


/* ... and all the other slideshow styles from the previous response */

        .slide-icon svg {
            width: 30px;
            height: 30px;
        }


        .content-slide-indicators {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
            z-index: 10;
        }

        .content-slide-indicator {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .content-slide-indicator.active {
            background: var(--primary);
            transform: scale(1.2);
        }

        /* Empty state styling */
        .slideshow-empty {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 12px;
            border: 1px solid rgba(236, 72, 153, 0.2);
        }

        .empty-content {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        .empty-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 0.5rem;
            color: rgba(236, 72, 153, 0.5);
        }

        /* Tablet and Desktop Styles */
        @media (min-width: 768px) {
            .content-slideshow {
                height: 280px;
            }
            
            .content-slide {
                padding: 2rem;
            }
            
            .slide-title {
                font-size: 1.5rem;
            }
            
            .slide-meta {
                font-size: 1rem;
            }
        }

        /* For smaller screens where space is limited */
        @media (max-width: 360px) {
            .slide-meta {
                font-size: 0.8rem;
                line-height: 1.3;
                margin-bottom: 0.75rem;
            }
        }

        /* Small Mobile Optimization */
        @media (max-width: 360px) {
            .content-slideshow {
                height: 250px;
            }
            
            .content-slide {
                padding: 1rem;
            }
            
            .slide-title {
                font-size: 1.1rem;
            }
            
            .slide-meta {
                font-size: 0.8rem;
            }
            
            .slide-icon {
                width: 50px;
                height: 50px;
            }
            
            .slide-icon svg {
                width: 24px;
                height: 24px;
            }
        }
        
        /* Quick Actions - Full Width */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 3rem;
            width: 100%;
        }
        
        .action-card {
            background: rgba(0, 0, 0, 0.8);
            padding: 2rem 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(236, 72, 153, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(236, 72, 153, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
        }
        
        .action-card.secondary::before {
            background: #000000;
        }
        
        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(236, 72, 153, 0.2);
            border-color: rgba(236, 72, 153, 0.4);
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #000000;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .action-card.secondary .action-icon {
            background: #000000;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .action-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        .action-card p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .action-card a {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .action-card a:hover {
            gap: 0.75rem;
            color: #ffffff;
        }
        
        .action-card.secondary a {
            color: #ffffff;
        }

        .action-card.secondary a:hover {
            color: var(--primary);
        }

        /* Feature Sections - Full Width */
        .feature-section {
            background: rgba(0, 0, 0, 0.9);
            padding: 2.5rem 0;
            margin-bottom: 3rem;
            width: 100%;
            border-top: 1px solid rgba(236, 72, 153, 0.2);
            border-bottom: 1px solid rgba(236, 72, 153, 0.2);
        }
        
        .feature-section h2.section-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
        }
        
        .feature-section h2.section-title::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .feature-card {
            border-radius: 12px;
            padding: 2rem 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.1) 0%, rgba(0, 0, 0, 0.8) 100%);
            z-index: 1;
        }
        
        .feature-card > * {
            position: relative;
            z-index: 2;
        }
        
        .feature-card.attendees {
            background: linear-gradient(135deg, var(--primary), #000000);
        }
        
        .feature-card.organizers {
            background: linear-gradient(135deg, #000000, var(--primary));
        }
        
        .feature-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .feature-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .feature-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            flex-shrink: 0;
            font-weight: 700;
            font-size: 1rem;
            color: var(--primary);
        }
        
        .organizers .step-number {
            color: #000000;
            background: var(--primary);
        }
        
        .step-content h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: white;
            line-height: 1.3;
        }
        
        .step-content p {
            font-size: 0.9rem;
            opacity: 0.95;
            line-height: 1.5;
            margin: 0;
        }

        /* Testimonials Section - Full Width */
        .testimonials-section {
            background: rgba(0, 0, 0, 0.9);
            padding: 2.5rem 0;
            width: 100%;
            border-top: 1px solid rgba(236, 72, 153, 0.2);
        }

        .testimonials-section h2.section-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .testimonials-section h2.section-title::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* Tablet and Desktop Styles */
        @media (min-width: 768px) {
            .testimonials-section h2.section-title {
                font-size: 2.5rem;
                margin-bottom: 3rem;
            }
            
            .testimonials-section h2.section-title::after {
                bottom: -1.5rem;
                width: 80px;
                height: 4px;
            }
        }

        .testimonials-slideshow {
            position: relative;
            width: 100%;
            margin: 0 auto;
            height: 400px;
        }
        
        .testimonial-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .testimonial-slide.active {
            opacity: 1;
            pointer-events: all;
        }
        
        .testimonial-card {
            background: rgba(0, 0, 0, 0.9);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            box-shadow: 0 10px 30px rgba(236, 72, 153, 0.2);
            border: 1px solid rgba(236, 72, 153, 0.3);
            text-align: center;
            width: 95%;
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 4rem;
            color: var(--primary);
            font-family: Georgia, serif;
            line-height: 1;
            opacity: 0.2;
        }
        
        .testimonial-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .testimonial-avatar {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            color: #000000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
        }
        
        .avatar-1 { background: linear-gradient(135deg, var(--primary), #ffffff); }
        .avatar-2 { background: linear-gradient(135deg, #000000, var(--primary)); }
        .avatar-3 { background: linear-gradient(135deg, var(--primary), #000000); }
        
        .testimonial-info {
            text-align: center;
        }
        
        .testimonial-info h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }
        
        .testimonial-info p {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .testimonial-text {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            font-size: 1rem;
            font-style: italic;
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 100%;
            margin: 0 auto;
        }
        
        .testimonial-indicators {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            position: absolute;
            bottom: 1rem;
            left: 0;
            right: 0;
        }
        
        .testimonial-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .testimonial-indicator.active {
            background: var(--primary);
            transform: scale(1.2);
        }
        
        /* Footer - Full Width */
        .main-footer {
            background: #000000;
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 4rem;
            width: 100%;
            border-top: 1px solid rgba(236, 72, 153, 0.3);
        }
        
        .footer-content {
            text-align: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .footer-content p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .footer-content p strong {
            color: var(--primary);
        }
        
        /* Modal Styles - Pink/Black Theme */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 1rem;
        }
        
        .modal-content {
            background: #000000;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(236, 72, 153, 0.3);
            width: 100%;
            max-width: 400px;
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
            border: 1px solid rgba(236, 72, 153, 0.3);
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--primary);
            padding: 0.25rem;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            min-width: 32px;
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            background: rgba(236, 72, 153, 0.1);
        }
        
        .modal-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid rgba(236, 72, 153, 0.3);
        }

        .modal-header h2 {
            color: #ffffff;
        }

        .modal-header p {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .modal-body {
            padding: 1.5rem 2rem 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #ffffff;
            font-size: 0.9rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(236, 72, 153, 0.3);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
        }
        
        .form-checkbox {
            margin-right: 0.5rem;
        }
        
        .form-footer {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
            margin-top: 1.5rem;
        }
        
        .form-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .form-link:hover {
            text-decoration: underline;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: #000000;
            border: 1px solid var(--primary);
            font-weight: 700;
        }
        
        .btn-primary:hover {
            background: #ffffff;
            border-color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            color: #ffffff;
            border: 1.5px solid var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: #000000;
        }
        
        /* Utility classes */
        [x-cloak] {
            display: none !important;
        }

        /* Tablet and Desktop Styles */
        @media (min-width: 768px) {
            body {
                font-size: 16px;
            }
            
            .mobile-nav {
                display: none;
            }
            
            .nav-blur {
                display: block;
                background: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(236, 72, 153, 0.3);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 1000;
            }
            
            .nav-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                height: 80px;
            }
            
            .nav-logo {
                font-size: 1.75rem;
                font-weight: 800;
                background: linear-gradient(135deg, var(--primary), #ffffff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                text-decoration: none;
                letter-spacing: -0.5px;
            }
            
            .nav-menu {
                display: flex;
                align-items: center;
                gap: 2rem;
            }
            
            .nav-link {
                color: #ffffff;
                font-weight: 600;
                font-size: 1rem;
                text-decoration: none;
                transition: color 0.3s ease;
                padding: 0.5rem 0;
                position: relative;
            }
            
            .nav-link:hover {
                color: var(--primary);
            }
            
            .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 2px;
                background: var(--primary);
                transition: width 0.3s ease;
            }
            
            .nav-link:hover::after {
                width: 100%;
            }
            
            .nav-buttons {
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            
            .main-content {
                padding-top: 80px;
            }
            
            .section-inner {
                padding: 0 2rem;
            }
            
            .slideshow-container {
                height: 500px;
            }
            
            .slide-content {
                padding: 3rem;
            }
            
            .slide-badge {
                font-size: 0.875rem;
                padding: 0.5rem 1.25rem;
                margin-bottom: 1rem;
            }
            
            .slide-content h2 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }
            
            .slide-content p {
                font-size: 1.25rem;
            }
            
            .content-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
                margin-bottom: 4rem;
            }
            
            .content-section {
                border-radius: 16px;
                padding: 2rem;
            }
            
            .section-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                padding-bottom: 1.5rem;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .content-item {
                flex-direction: row;
                text-align: left;
                padding: 1.25rem;
                margin-bottom: 1rem;
            }
            
            .item-image {
                width: 70px;
                height: 70px;
                margin-right: 1.5rem;
                margin-bottom: 0;
                font-size: 1.5rem;
            }
            
            .item-title {
                font-size: 1.1rem;
                margin-bottom: 0.5rem;
            }
            
            .item-meta {
                justify-content: flex-start;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
                margin-bottom: 4rem;
            }
            
            .action-card {
                padding: 2.5rem 2rem;
                border-radius: 16px;
            }
            
            .action-icon {
                width: 70px;
                height: 70px;
                font-size: 1.75rem;
                margin-bottom: 2rem;
            }
            
            .action-card h3 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .action-card p {
                font-size: 1rem;
                margin-bottom: 2rem;
            }
            
            .feature-section {
                padding: 4rem 0;
            }
            
            .feature-section h2.section-title {
                font-size: 2.5rem;
                margin-bottom: 3rem;
            }
            
            .feature-section h2.section-title::after {
                bottom: -1.5rem;
                width: 80px;
                height: 4px;
            }
            
            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 3rem;
            }
            
            .feature-card {
                border-radius: 16px;
                padding: 2.5rem 2rem;
            }
            
            .feature-header {
                flex-direction: row;
                text-align: left;
                margin-bottom: 2.5rem;
            }
            
            .feature-icon {
                width: 70px;
                height: 70px;
                margin-right: 1.5rem;
                margin-bottom: 0;
            }
            
            .feature-header h3 {
                font-size: 1.75rem;
            }
            
            .feature-step {
                flex-direction: row;
                text-align: left;
                margin-bottom: 2rem;
                padding: 1.5rem;
            }
            
            .step-number {
                width: 50px;
                height: 50px;
                margin-right: 1.5rem;
                margin-bottom: 0;
                font-size: 1.25rem;
            }
            
            .step-content h4 {
                font-size: 1.2rem;
                margin-bottom: 0.5rem;
            }
            
            .step-content p {
                font-size: 1rem;
            }
            
            .testimonials-section {
                padding: 4rem 0;
            }
            
            .testimonials-slideshow {
                max-width: 800px;
                height: 400px;
            }
            
            .testimonial-card {
                border-radius: 20px;
                padding: 3rem 2.5rem;
                max-width: 600px;
            }
            
            .testimonial-card::before {
                top: 1.5rem;
                right: 2rem;
                font-size: 5rem;
            }
            
            .testimonial-header {
                flex-direction: row;
                margin-bottom: 2rem;
            }
            
            .testimonial-avatar {
                width: 80px;
                height: 80px;
                margin-right: 1.5rem;
                margin-bottom: 0;
                font-size: 1.5rem;
            }
            
            .testimonial-info {
                text-align: left;
            }
            
            .testimonial-info h4 {
                font-size: 1.4rem;
            }
            
            .testimonial-info p {
                font-size: 1rem;
            }
            
            .testimonial-text {
                font-size: 1.1rem;
                max-width: 500px;
            }
            
            .main-footer {
                padding: 4rem 0 2rem;
                margin-top: 6rem;
            }
        }

        /* Small Mobile Optimization - Squeezed Content */
        @media (max-width: 360px) {
            .section-inner {
                padding: 0 0.5rem;
            }
            
            .slideshow-container {
                height: 280px;
            }
            
            .slide-content {
                padding: 1rem;
            }
            
            .slide-content h2 {
                font-size: 1.3rem;
            }
            
            .slide-content p {
                font-size: 0.8rem;
            }
            
            .content-section {
                padding: 1rem;
                margin: 0.5rem;
            }
            
            .feature-section {
                padding: 1.5rem 0;
            }
            
            .action-card {
                padding: 1.5rem 1rem;
                margin: 0.5rem;
            }
            
            .mobile-btn {
                padding: 0.4rem 0.6rem;
                font-size: 0.75rem;
                min-height: 36px;
            }
            
            .content-item {
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }
            
            .item-image {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }
            
            .feature-step {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .testimonial-card {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>

<body x-data="{
        currentSlide: 0,
        totalSlides: 4,
        testimonialSlide: 0,
        totalTestimonials: 3,
        autoSlide: true,
        testimonialAutoSlide: true,
        signupOpen: false,
        loginOpen: false,
        mobileMenuOpen: false,
        intendedUrl: '{{ route('organizers.index') }}'
    }" 
    x-init="
        setInterval(() => {
            if (autoSlide) {
                currentSlide = (currentSlide + 1) % totalSlides;
            }
        }, 5000);
        setInterval(() => {
            if (testimonialAutoSlide) {
                testimonialSlide = (testimonialSlide + 1) % totalTestimonials;
            }
        }, 6000)
    " 
    class="font-sans antialiased">

    <!-- Desktop Navigation -->
    <nav class="nav-blur">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="nav-logo">Javent</a>
            
            <div class="nav-menu">
                <a href="{{ route('events.index') }}" class="nav-link">Events</a>
                <a href="{{ route('voting.index') }}" class="nav-link">Voting</a>
                <a href="{{ route('organizers.index') }}" class="nav-link">For Organizers</a>
            </div>
            
            <div class="nav-buttons">
                @auth
                    <span class="text-pink-400 font-medium">Welcome, {{ auth()->user()->name }}</span>
                @else
                    <button @click="loginOpen = true" class="btn btn-outline">Login</button>
                    <button @click="signupOpen = true" class="btn btn-primary">Sign Up</button>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <nav class="mobile-nav">
        <a href="{{ route('home') }}" class="mobile-nav-logo">Javent</a>
        
        <div class="mobile-nav-buttons">
            @auth
                <span class="text-pink-400 font-medium text-xs">Hi, {{ auth()->user()->name }}</span>
            @else
                <button @click="loginOpen = true" class="mobile-btn mobile-btn-outline">Login</button>
                <button @click="signupOpen = true" class="mobile-btn mobile-btn-primary">Sign Up</button>
            @endauth
            
            <button class="mobile-menu-toggle" 
                    :class="{ 'active': mobileMenuOpen }"
                    @click="mobileMenuOpen = !mobileMenuOpen">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" :class="{ 'active': mobileMenuOpen }">
        <a href="{{ route('events.index') }}" class="mobile-menu-link" @click="mobileMenuOpen = false">Events</a>
        <a href="{{ route('voting.index') }}" class="mobile-menu-link" @click="mobileMenuOpen = false">Voting</a>
        <a href="{{ route('organizers.index') }}" class="mobile-menu-link" @click="mobileMenuOpen = false">For Organizers</a>
    </div>

    <!-- Modals remain the same but now with pink/black theme -->
    <!-- Sign Up Modal -->
    <template x-if="signupOpen">
        <div class="modal-overlay" @click="signupOpen = false">
            <div class="modal-content" @click.stop>
                <button class="modal-close" @click="signupOpen = false">&times;</button>
                <div class="modal-header">
                    <h2 class="text-2xl font-bold">Create Account</h2>
                    <p class="mt-1">Join Javent to discover amazing events</p>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input id="name" type="text" name="name" :value="old('name')" required autofocus class="form-input" placeholder="Enter your full name" />
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" name="email" :value="old('email')" required class="form-input" placeholder="Enter your email" />
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" required class="form-input" placeholder="Create a password" />
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input" placeholder="Confirm your password" />
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">Create Account</button>
                            <span class="text-sm" style="color: rgba(255,255,255,0.7);">
                                Already have an account?
                                <button type="button" @click="signupOpen = false; loginOpen = true" class="form-link">Log in</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Login Modal -->
    <template x-if="loginOpen">
        <div class="modal-overlay" @click="loginOpen = false">
            <div class="modal-content" @click.stop>
                <button class="modal-close" @click="loginOpen = false">&times;</button>
                <div class="modal-header">
                    <h2 class="text-2xl font-bold">Welcome Back</h2>
                    <p class="mt-1">Sign in to your Javent account</p>
                </div>
                <div class="modal-body">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-400 bg-green-900 bg-opacity-20 p-3 rounded-lg">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus class="form-input" placeholder="Enter your email" />
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" required class="form-input" placeholder="Enter your password" />
                        </div>
                        <div class="form-group">
                            <div class="flex items-center">
                                <input id="remember_me" type="checkbox" name="remember" class="form-checkbox" />
                                <label for="remember_me" class="ml-2 text-sm" style="color: rgba(255,255,255,0.7);">Remember me</label>
                            </div>
                        </div>
                        <div class="form-footer">
                            <span class="text-sm" style="color: rgba(255,255,255,0.7);">
                                Don't have an account?
                                <button type="button" @click="loginOpen = false; signupOpen = true" class="form-link">Sign up</button>
                            </span>
                            <button type="submit" class="btn btn-primary">Log in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Full Width Slideshow -->
        <div class="full-width-section">
            <div class="slideshow-container">
                <!-- Slide 1 - Events -->
                <div class="slide" :class="{ 'active': currentSlide === 0 }" 
                    style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                            @if(file_exists(public_path('images/slide1-events.jpg'))) url('{{ asset('images/slide1-events.jpg') }}') @else linear-gradient(135deg, #ec4899 0%, #be185d 100%) @endif;
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;">
                    <div class="slide-content">
                        <span class="slide-badge">Featured Events</span>
                        <h2>Discover Amazing Events Around You</h2>
                        <p>Find and book tickets for incredible events that match your interests and passions</p>
                    </div>
                </div>
                
                <!-- Slide 2 - Voting -->
                <div class="slide" :class="{ 'active': currentSlide === 1 }" 
                    style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                            @if(file_exists(public_path('images/slide2-voting.jpg'))) url('{{ asset('images/slide2-voting.jpg') }}') @else linear-gradient(135deg, #000000 0%, #ec4899 100%) @endif;
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;">
                    <div class="slide-content">
                        <span class="slide-badge">Live Voting</span>
                        <h2>Participate in Exciting Contests</h2>
                        <p>Vote in thrilling competitions and watch real-time results unfold</p>
                    </div>
                </div>
                
                <!-- Slide 3 - Mixed -->
                <div class="slide" :class="{ 'active': currentSlide === 2 }" 
                    style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                            @if(file_exists(public_path('images/slide3-trending.jpg'))) url('{{ asset('images/slide3-trending.jpg') }}') @else linear-gradient(135deg, #ec4899 0%, #000000 100%) @endif;
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;">
                    <div class="slide-content">
                        <span class="slide-badge">Trending Now</span>
                        <h2>All in One Powerful Platform</h2>
                        <p>Events and voting seamlessly combined for the ultimate experience</p>
                    </div>
                </div>
                
                <!-- Slide 4 - Organizers -->
                <div class="slide" :class="{ 'active': currentSlide === 3 }" 
                    style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                            @if(file_exists(public_path('images/slide4-organizers.jpg'))) url('{{ asset('images/slide4-organizers.jpg') }}') @else linear-gradient(135deg, #be185d 0%, #000000 100%) @endif;
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;">
                    <div class="slide-content">
                        <span class="slide-badge">For Organizers</span>
                        <h2>Create Unforgettable Events</h2>
                        <p>Powerful tools to manage, promote, and grow your events</p>
                    </div>
                </div>
                
                <!-- Slide Indicators -->
                <div class="slide-indicators">
                    <template x-for="i in totalSlides">
                        <div :class="{ 'active': currentSlide === i-1 }" 
                            class="slide-indicator"
                            @click="currentSlide = i-1; autoSlide = false; setTimeout(() => autoSlide = true, 10000)"></div>
                    </template>
                </div>
            </div>
        </div>

       <!-- Content Grid -->
       <!-- Events Section - Full Width -->
        <div class="full-width-section">
            <div class="section-inner">
                <div class="content-section">
                    <div class="section-header">
                        <h3 class="section-title">Featured Events</h3>
                        <a href="{{ route('events.index') }}" class="view-all">View All →</a>
                    </div>
                    <!-- Events Slideshow will be loaded by Livewire -->
                    <div id="events-slideshow">
                        <livewire:event-feed />
                    </div>
                </div>
            </div>
        </div>

        <!-- Voting Section - Full Width -->
        <div class="full-width-section">
            <div class="section-inner">
                <div class="content-section voting">
                    <div class="section-header">
                        <h3 class="section-title">Active Voting</h3>
                        <a href="{{ route('voting.index') }}" class="view-all">View All →</a>
                    </div>
                    <!-- Voting Slideshow will be loaded by Livewire -->
                    <div id="voting-slideshow">
                        <livewire:voting-feed />
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="full-width-section">
            <div class="section-inner">
                <div class="quick-actions">
                    <div class="action-card">
                        <div class="action-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3>Browse Events</h3>
                        <p>Discover amazing upcoming events and unforgettable experiences in your area and beyond</p>
                        <a href="{{ route('events.index') }}">Explore Events →</a>
                    </div>
                    
                    <div class="action-card">
                        <div class="action-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3>Join Voting</h3>
                        <p>Participate in live contests, prestigious awards and help shape the final outcomes</p>
                        <a href="{{ route('voting.index') }}">Vote Now →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="feature-section">
            <div class="section-inner">
                <h2 class="section-title">How Javent Works</h2>
                <div class="feature-grid">
                    <!-- For Attendees Column -->
                    <div class="feature-card attendees">
                        <div class="feature-header">
                            <div class="feature-icon">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3>For Attendees & Voters</h3>
                        </div>
                        <div class="space-y-6">
                            <div class="feature-step">
                                <span class="step-number">1</span>
                                <div class="step-content">
                                    <h4>Discover Amazing Events</h4>
                                    <p>Browse through carefully curated events and exciting voting contests happening in your area and online</p>
                                </div>
                            </div>
                            <div class="feature-step">
                                <span class="step-number">2</span>
                                <div class="step-content">
                                    <h4>Book Tickets or Cast Votes</h4>
                                    <p>Secure your spot with our easy booking system or participate in live voting with real-time updates</p>
                                </div>
                            </div>
                            <div class="feature-step">
                                <span class="step-number">3</span>
                                <div class="step-content">
                                    <h4>Enjoy & Engage</h4>
                                    <p>Attend unforgettable events and watch as your votes help shape competition outcomes and winners</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- For Organizers Column -->
                    <div class="feature-card organizers">
                        <div class="feature-header">
                            <div class="feature-icon">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <h3>For Organizers</h3>
                        </div>
                        <div class="space-y-6">
                            <div class="feature-step">
                                <span class="step-number">1</span>
                                <div class="step-content">
                                    <h4>Create Your Event</h4>
                                    <p>Set up professional events with comprehensive ticketing, seating arrangements, and voting options</p>
                                </div>
                            </div>
                            <div class="feature-step">
                                <span class="step-number">2</span>
                                <div class="step-content">
                                    <h4>Manage & Promote</h4>
                                    <p>Use our advanced tools to efficiently manage attendees and effectively promote your event</p>
                                </div>
                            </div>
                            <div class="feature-step">
                                <span class="step-number">3</span>
                                <div class="step-content">
                                    <h4>Analyze & Grow</h4>
                                    <p>Gain valuable insights from detailed analytics to continuously improve your future events</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Quick Actions -->
        <div class="full-width-section">
            <div class="section-inner">
                <div class="quick-actions">
                    <div class="action-card">
                        <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3>Create Event</h3>
                        <p>Start organizing your own amazing events and engaging voting contests with our powerful platform</p>
                        <a href="{{ route('organizers.index') }}" style="color: #10b981;">Get Started →</a>
                    </div>
                    
                    <div class="action-card">
                        <div class="action-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3>Learn More</h3>
                        <p>Discover how Javent can completely transform your event creation and management experience</p>
                        <a href="{{ route('about') }}" style="color: #8b5cf6;">Learn More →</a>
                    </div>
                </div>
            </div>
        </div>

       <!-- Testimonials Section -->
        <div class="testimonials-section">
            <div class="section-inner">
                <h2 class="section-title">What Our Community Says</h2>
                <div class="testimonials-slideshow">
                    <!-- Testimonial 1 -->
                    <div class="testimonial-slide" :class="{ 'active': testimonialSlide === 0 }">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="testimonial-avatar avatar-1">AJ</div>
                                <div class="testimonial-info">
                                    <h4>Aisha Johnson</h4>
                                    <p>Event Attendee & Regular Voter</p>
                                </div>
                            </div>
                            <p class="testimonial-text">"Booking tickets through Javent is incredibly seamless. The QR check-in system made event entry an absolute breeze! The entire experience from discovery to attendance was smooth, professional, and thoroughly enjoyable."</p>
                        </div>
                    </div>
                    
                    <!-- Testimonial 2 -->
                    <div class="testimonial-slide" :class="{ 'active': testimonialSlide === 1 }">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="testimonial-avatar avatar-2">MK</div>
                                <div class="testimonial-info">
                                    <h4>Mark Thompson</h4>
                                    <p>Awards Ceremony Organizer</p>
                                </div>
                            </div>
                            <p class="testimonial-text">"The voting system completely transformed our annual awards night. Real-time results kept everyone engaged and created an unforgettable, interactive experience for our 500+ attendees. The platform's reliability was exceptional."</p>
                        </div>
                    </div>
                    
                    <!-- Testimonial 3 -->
                    <div class="testimonial-slide" :class="{ 'active': testimonialSlide === 2 }">
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="testimonial-avatar avatar-3">SM</div>
                                <div class="testimonial-info">
                                    <h4>Sarah Martinez</h4>
                                    <p>Music Festival Director</p>
                                </div>
                            </div>
                            <p class="testimonial-text">"From comprehensive ticketing to efficient vendor management, Javent handles everything we need. It's been a complete game-changer that saved us countless hours of manual work while significantly improving our attendees' experience."</p>
                        </div>
                    </div>
                    
                    <!-- Testimonial Indicators -->
                    <div class="testimonial-indicators">
                        <template x-for="i in totalTestimonials">
                            <div :class="{ 'active': testimonialSlide === i-1 }" 
                                class="testimonial-indicator"
                                @click="testimonialSlide = i-1; testimonialAutoSlide = false; setTimeout(() => testimonialAutoSlide = true, 10000)"></div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p>&copy; 2025 <strong>Javent</strong>. Premium Events & Voting Platform. All rights reserved.</p>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>