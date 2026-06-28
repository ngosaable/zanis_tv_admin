<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Zanis TV Admin') }} - DSTV Style</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            /* DSTV Inspired Color Scheme */
            --dstv-blue: #003A70;
            --dstv-blue-dark: #002845;
            --dstv-blue-light: #005A9E;
            --dstv-gold: #FFB81C;
            --dstv-gold-light: #FFD666;
            --dstv-gold-dark: #E69A00;
            
            /* Section Colors */
            --video-color: #3B82F6;
            --video-light: #DBEAFE;
            --live-color: #F97316;
            --live-light: #FFEDD5;
            --category-color: #10B981;
            --category-light: #D1FAE5;
            --slider-color: #8B5CF6;
            --slider-light: #EDE9FE;
            --ad-color: #EF4444;
            --ad-light: #FEE2E2;
            
            /* UI Colors */
            --bg-primary: #F5F7FA;
            --bg-white: #FFFFFF;
            --bg-sidebar: var(--dstv-blue);
            --bg-card: #FFFFFF;
            --border-light: #E5E9EF;
            --text-primary: #1A1A2E;
            --text-secondary: #6B7280;
            --text-light: #9CA3AF;
            --text-white: #FFFFFF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Layout */
        .dstv-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .dstv-sidebar {
            width: 260px;
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .dstv-logo {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dstv-logo img {
            height: 40px;
            width: auto;
        }

        .dstv-logo-text {
            color: var(--text-white);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .dstv-logo-text span {
            color: var(--dstv-gold);
        }

        .dstv-sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px 12px;
        }

        .dstv-menu-section {
            margin-bottom: 28px;
        }

        .dstv-menu-title {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 0 12px;
            margin-bottom: 10px;
        }

        .dstv-menu {
            list-style: none;
        }

        .dstv-menu-item {
            margin-bottom: 4px;
        }

        .dstv-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dstv-menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-white);
        }

        .dstv-menu-link.active {
            background: var(--dstv-gold);
            color: var(--dstv-blue-dark);
            font-weight: 600;
        }

        .dstv-menu-icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .dstv-menu-arrow {
            margin-left: auto;
            font-size: 10px;
        }

        /* User Section */
        .dstv-user-section {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dstv-user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .dstv-user-avatar {
            width: 40px;
            height: 40px;
            background: var(--dstv-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--dstv-blue-dark);
            font-size: 14px;
        }

        .dstv-user-info {
            flex: 1;
            min-width: 0;
        }

        .dstv-user-name {
            color: var(--text-white);
            font-weight: 600;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dstv-user-email {
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dstv-logout-btn {
            width: 100%;
            padding: 10px;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #FCA5A5;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            margin-top: 12px;
        }

        .dstv-logout-btn:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        /* Main Content */
        .dstv-main {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .dstv-header {
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .dstv-header-left {
            display: flex;
            flex-direction: column;
        }

        .dstv-page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--dstv-blue);
            margin: 0;
        }

        .dstv-page-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .dstv-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dstv-search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: var(--bg-primary);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            min-width: 280px;
        }

        .dstv-search-box input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--text-primary);
            font-size: 14px;
            width: 100%;
        }

        .dstv-search-box input::placeholder {
            color: var(--text-light);
        }

        .dstv-search-box i {
            color: var(--text-light);
        }

        .dstv-icon-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-primary);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .dstv-icon-btn:hover {
            background: var(--dstv-blue);
            color: white;
            border-color: var(--dstv-blue);
        }

        .dstv-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--danger);
            color: white;
            font-size: 10px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Content Area */
        .dstv-content {
            padding: 24px 32px;
            flex: 1;
        }

        /* Stats Grid */
        .dstv-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 1200px) {
            .dstv-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dstv-stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .dstv-stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .dstv-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .dstv-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .dstv-stat-icon.blue {
            background: rgba(0, 58, 112, 0.1);
            color: var(--dstv-blue);
        }

        .dstv-stat-icon.gold {
            background: rgba(255, 184, 28, 0.1);
            color: var(--dstv-gold-dark);
        }

        .dstv-stat-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .dstv-stat-icon.cyan {
            background: rgba(6, 182, 212, 0.1);
            color: var(--info);
        }

        .dstv-stat-icon.purple {
            background: rgba(139, 92, 246, 0.1);
            color: #8B5CF6;
        }

        .dstv-stat-icon.orange {
            background: rgba(249, 115, 22, 0.1);
            color: #F97316;
        }

        .dstv-stat-icon.pink {
            background: rgba(236, 72, 153, 0.1);
            color: #EC4899;
        }

        .dstv-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        .dstv-stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        /* Cards */
        .dstv-card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .dstv-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dstv-card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dstv-card-title i {
            color: var(--dstv-blue);
        }

        .dstv-card-body {
            padding: 0;
        }

        .dstv-view-all {
            font-size: 13px;
            color: var(--dstv-blue);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .dstv-view-all:hover {
            color: var(--dstv-gold-dark);
        }

        /* Tables */
        .dstv-table {
            width: 100%;
            border-collapse: collapse;
        }

        .dstv-table th {
            padding: 14px 24px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-light);
        }

        .dstv-table td {
            padding: 16px 24px;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-light);
        }

        .dstv-table tr:last-child td {
            border-bottom: none;
        }

        .dstv-table tr:hover {
            background: rgba(0, 58, 112, 0.02);
        }

        /* Status Badges */
        .dstv-badge {
            padding: 5px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .dstv-badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .dstv-badge-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .dstv-badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .dstv-badge-info {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .dstv-badge-secondary {
            background: rgba(107, 114, 128, 0.1);
            color: #6B7280;
        }

        /* Buttons */
        .dstv-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }

        .dstv-btn-primary {
            background: var(--dstv-blue);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 58, 112, 0.3);
        }

        .dstv-btn-primary:hover {
            background: var(--dstv-blue-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 58, 112, 0.4);
        }

        .dstv-btn-gold {
            background: var(--dstv-gold);
            color: var(--dstv-blue-dark);
            box-shadow: 0 2px 8px rgba(255, 184, 28, 0.3);
        }

        .dstv-btn-gold:hover {
            background: var(--dstv-gold-light);
            transform: translateY(-1px);
        }

        .dstv-btn-outline {
            background: transparent;
            border: 1px solid var(--border-light);
            color: var(--text-secondary);
        }

        .dstv-btn-outline:hover {
            border-color: var(--dstv-blue);
            color: var(--dstv-blue);
            background: rgba(0, 58, 112, 0.05);
        }

        .dstv-btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Action Buttons */
        .dstv-action-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--bg-primary);
            border: 1px solid var(--border-light);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 13px;
        }

        .dstv-action-btn:hover {
            background: var(--dstv-blue);
            color: white;
            border-color: var(--dstv-blue);
        }

        .dstv-action-btn.delete:hover {
            background: var(--danger);
            border-color: var(--danger);
        }

        /* Alerts */
        .dstv-alert {
            padding: 16px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .dstv-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
        }

        .dstv-alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger);
        }

        /* Empty State */
        .dstv-empty {
            text-align: center;
            padding: 48px 20px;
        }

        .dstv-empty i {
            font-size: 48px;
            color: var(--text-light);
            margin-bottom: 12px;
            display: block;
        }

        .dstv-empty h3 {
            color: var(--text-primary);
            font-size: 16px;
            margin-bottom: 6px;
        }

        .dstv-empty p {
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        /* Activity Items */
        .dstv-activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border-light);
            text-decoration: none;
            transition: background 0.2s;
        }

        .dstv-activity-item:last-child {
            border-bottom: none;
        }

        .dstv-activity-item:hover {
            background: rgba(0, 58, 112, 0.03);
        }

        .dstv-activity-thumb {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
        }

        .dstv-activity-thumb-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
        }

        .dstv-activity-info {
            flex: 1;
            min-width: 0;
        }

        .dstv-activity-title {
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 14px;
        }

        .dstv-activity-meta {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Channel Items */
        .dstv-channel-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border-light);
        }

        .dstv-channel-item:last-child {
            border-bottom: none;
        }

        .dstv-channel-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 184, 28, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dstv-gold-dark);
        }

        .dstv-channel-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }

        .dstv-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--danger);
            animation: pulse 2s infinite;
        }

        .dstv-live-badge i {
            font-size: 6px;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Platform Stats */
        .dstv-platform-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border-light);
        }

        .dstv-platform-item:last-child {
            border-bottom: none;
        }

        .dstv-platform-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .dstv-platform-label i {
            width: 18px;
            text-align: center;
            color: var(--dstv-blue);
        }

        .dstv-platform-value {
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Category List */
        .dstv-category-list {
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .dstv-category-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dstv-category-name {
            width: 80px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .dstv-category-bar {
            flex: 1;
            height: 8px;
            background: var(--bg-primary);
            border-radius: 4px;
            overflow: hidden;
        }

        .dstv-category-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--dstv-blue), var(--dstv-blue-light));
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .dstv-category-count {
            width: 30px;
            text-align: right;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
        }

        /* Quick Actions */
        .dstv-quick-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 16px 24px;
        }

        .dstv-quick-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 16px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dstv-quick-btn:hover {
            border-color: var(--dstv-blue);
            color: var(--dstv-blue);
            background: rgba(0, 58, 112, 0.05);
        }

        .dstv-quick-btn i {
            font-size: 18px;
        }

        /* Dashboard Grid */
        .dstv-dashboard-main {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .dstv-dashboard-main {
                grid-template-columns: 1fr;
            }
        }

        .dstv-dashboard-left, .dstv-dashboard-right {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Form Elements */
        .dstv-form-group {
            margin-bottom: 20px;
        }

        .dstv-form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .dstv-form-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-white);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .dstv-form-input:focus {
            border-color: var(--dstv-blue);
            box-shadow: 0 0 0 3px rgba(0, 58, 112, 0.1);
        }

        .dstv-form-input::placeholder {
            color: var(--text-light);
        }

        .dstv-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
        }

        .dstv-required {
            color: var(--danger);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dstv-sidebar {
                display: none;
            }

            .dstv-main {
                margin-left: 0;
            }

            .dstv-header {
                padding: 16px 20px;
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .dstv-header-right {
                width: 100%;
            }

            .dstv-search-box {
                display: none;
            }

            .dstv-content {
                padding: 16px;
            }
        }
    </style>
    <style>
        .dstv-toggle-slider {
            width: 44px;
            height: 24px;
            background: var(--border-light);
            border-radius: 12px;
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
            cursor: pointer;
        }
        .dstv-toggle-knob {
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        input:checked + .dstv-toggle-slider {
            background: var(--video-color, #3b82f6);
        }
        input:checked + .dstv-toggle-slider .dstv-toggle-knob {
            transform: translateX(22px);
        }
    </style>
    @stack('styles')
</head>
<body class="dstv-layout">
    <!-- Sidebar -->
    <aside class="dstv-sidebar">
        <div class="dstv-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Zanis TV">
            <span class="dstv-logo-text">Zanis <span>TV</span></span>
        </div>

        <div class="dstv-sidebar-content">
            <div class="dstv-menu-section">
                <div class="dstv-menu-title">Main</div>
                <ul class="dstv-menu">
                    <li class="dstv-menu-item">
                        <a href="{{ route('admin.dashboard') }}" class="dstv-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-th-large dstv-menu-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dstv-menu-section">
                <div class="dstv-menu-title">Content Management</div>
                <ul class="dstv-menu">
                    <li class="dstv-menu-item">
                        <a href="{{ route('admin.videos.index') }}" class="dstv-menu-link {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
                            <i class="fas fa-film dstv-menu-icon"></i>
                            <span>Movies & Videos</span>
                            <i class="fas fa-chevron-right dstv-menu-arrow"></i>
                        </a>
                    </li>
                    <li class="dstv-menu-item">
                        <a href="{{ route('admin.live-channels.index') }}" class="dstv-menu-link {{ request()->routeIs('admin.live-channels.*') ? 'active' : '' }}">
                            <i class="fas fa-satellite-dish dstv-menu-icon"></i>
                            <span>Live TV Channels</span>
                            <i class="fas fa-chevron-right dstv-menu-arrow"></i>
                        </a>
                    </li>
                    <li class="dstv-menu-item">
                        <a href="{{ route('admin.categories.index') }}" class="dstv-menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder-open dstv-menu-icon"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="dstv-menu-item">
                        <a href="{{ route('admin.sliders.index') }}" class="dstv-menu-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                            <i class="fas fa-images dstv-menu-icon"></i>
                            <span>Slider Banners</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dstv-menu-section">
                <div class="dstv-menu-title">Revenue</div>
                <ul class="dstv-menu">
                    <li class="dstv-menu-item">
                        <a href="{{ route('admin.ads.index') }}" class="dstv-menu-link {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
                            <i class="fas fa-ad dstv-menu-icon"></i>
                            <span>Ads Manager</span>
                        </a>
                    </li>
            </ul>
        </div>
    </div>

    <div class="dstv-menu-section">
        <div class="dstv-menu-title">System</div>
        <ul class="dstv-menu">
            <li class="dstv-menu-item">
                <a href="{{ route('admin.users.index') }}" class="dstv-menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users dstv-menu-icon"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="dstv-menu-item">
                <a href="{{ route('admin.roles.index') }}" class="dstv-menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag dstv-menu-icon"></i>
                    <span>Roles</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="dstv-user-section">
            <div class="dstv-user-card">
                <div class="dstv-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="dstv-user-info">
                    <div class="dstv-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="dstv-user-email">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dstv-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dstv-main">
        <header class="dstv-header">
            <div class="dstv-header-left">
                <h1 class="dstv-page-title">
                    @yield('page_title', 'Dashboard')
                </h1>
                <p class="dstv-page-subtitle">
                    @yield('page_subtitle', 'Welcome to Zanis TV Admin Panel')
                </p>
            </div>
            <div class="dstv-header-right">
                @yield('page_actions')
                <div class="dstv-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search content...">
                </div>
                <button class="dstv-icon-btn">
                    <i class="fas fa-bell"></i>
                    <span class="dstv-badge">5</span>
                </button>
            </div>
        </header>

        <div class="dstv-content">
            @if(session('success'))
                <div class="dstv-alert dstv-alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="dstv-alert dstv-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>