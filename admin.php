<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>پنل مدیریت کافه ریمچ</title>
    <style>
        /* ==========================================
           تنظیمات فونت سازگار با کروم‌های قدیمی و آفلاین
           ========================================== */
        @font-face {
            font-family: 'Lalezar';
            src: url('fonts/Digi%20Lalezar%20Plus.ttf') format('truetype'),
                 url('fonts/Digi%20Lalezar%20Plus.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Poppins';
            src: url('fonts/PoppinsLatin-Medium.ttf') format('truetype'),
                 url('fonts/PoppinsLatin-Medium.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        :root { 
            --primary: #80b918; 
            --bg-body: #121212; 
            --bg-panel: #1e1e1e; 
            --text-main: #f1f8e9; 
            --danger: #ff4757; 
            --info: #3742fa; 
            --success: #2ed573;
            --warning: #ffa502;
        }
        
        * { margin:0; padding:0; box-sizing:border-box; outline:none; font-family:'Lalezar', Tahoma, Arial, sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background:var(--bg-body); color:var(--text-main); display:flex; min-height:100vh; overflow-x:hidden; }
        
        /* Sidebar */
        .sidebar { width:260px; background:var(--bg-panel); height:100vh; position:fixed; right:0; top:0; border-left:1px solid #333; z-index:100; transition:0.3s; display:flex; flex-direction:column; }
        .sidebar.hide { transform: translateX(100%); }
        .nav-item { padding:1rem; cursor:pointer; color:#aaa; display:flex; align-items:center; transition:0.3s; margin:5px 10px; border-radius:10px; }
        .nav-item svg { margin-left: 12px; flex-shrink: 0; }
        .nav-item:hover, .nav-item.active { background:rgba(128,185,24,0.15); color:var(--primary); }
        
        /* Main Content */
        .main-content { margin-right:260px; width:calc(100% - 260px); padding:1.5rem; transition:0.3s; }
        .view-section { display:none; padding-bottom: 80px; contain: content; }
        .view-section.active { display:block; animation:fadeIn 0.18s ease-out; }
        @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
        
        /* Cards & Stats */
        .stat-card { background:var(--bg-panel); padding:1.2rem; border-radius:16px; border:1px solid #333; display:flex; justify-content:space-between; align-items:center; position:relative; overflow:hidden; margin-bottom: 10px;}
        .stat-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; margin-bottom:2rem; }
        .stat-value { font-size: 1.7rem; color: #fff; margin-bottom: 5px; font-weight: bold; }
        .stat-label { color: #888; font-size: 0.95rem; }
        
        /* Table */
        table { width:100%; border-collapse:collapse; white-space:nowrap; }
        .table-box { background:var(--bg-panel); border-radius:16px; overflow-x:auto; border:1px solid #333; }
        th, td { padding:1rem; text-align:right; border-bottom:1px solid #333; vertical-align: middle; }
        th { color:var(--primary); }
        .td-flex { display: flex; align-items: center; }
        .td-flex > * { margin-left: 10px; }
        .desc-text { max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.85rem; color: #aaa; }
        
        /* Buttons */
        .btn { background:var(--primary); padding:10px 20px; border-radius:50px; border:none; cursor:pointer; color:#000; display:flex; align-items:center; font-size:1rem; transition: 0.2s; font-weight: bold; }
        .btn svg { margin-left: 6px; }
        .btn:active { transform: scale(0.95); }
        .btn-outline { background:transparent; border:1px solid var(--primary); color:var(--primary); padding:10px 20px; border-radius:50px; cursor:pointer; display:flex; align-items:center; transition:0.2s; }
        .btn-outline svg { margin-left: 6px; }
        .btn-outline:hover { background: rgba(128,185,24,0.1); }
        .btn-sm { padding:8px 12px; border-radius:8px; background:#333; color:#fff; border:none; cursor:pointer; transition: 0.2s; display:inline-flex; align-items:center; justify-content:center;}
        .btn-sm svg { margin-left: 4px; }
        .btn-sm:hover { background: #444; }
        .inp { width:100%; padding:12px; background:#111; border:1px solid #333; color:#fff; border-radius:12px; margin-bottom:10px; }
        .inp:focus { border-color: var(--primary); }
        
        /* Order Controls */
        .order-controls { display: flex; align-items: center; background: #111; padding: 4px; border-radius: 10px; border: 1px solid #333; }
        .order-controls > * { margin: 0 2px; }
        .order-btn { width: 30px; height: 30px; border-radius: 6px; border: none; background: #222; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .order-btn:hover { background: var(--primary); color: #000; }
        .order-btn:disabled { opacity: 0.3; cursor: not-allowed; background: #222; color: #fff; }

        /* Modal */
        .modal-overlay { position:fixed; top:0; left:0; right:0; bottom:0; width:100%; height:100%; background:rgba(0,0,0,0.8); display:none; justify-content:center; align-items:center; z-index:999; backdrop-filter:blur(5px); }
        .modal-overlay.open { display:flex; }
        .modal-box { background:var(--bg-panel); padding:2rem; border-radius:20px; width:90%; max-width:600px; border:1px solid #333; max-height: 90vh; overflow-y: auto;}
        
        /* POS & Queue Styles */
        .pos-container { display: grid; grid-template-columns: 2fr 1.1fr; gap: 15px; height: calc(100vh - 80px); overflow: hidden; }
        .pos-products { overflow-y: auto; padding-right: 5px; display: flex; flex-direction: column; }
        .pos-cart { background: var(--bg-panel); border: 1px solid #333; border-radius: 16px; display: flex; flex-direction: column; height: 100%; overflow: hidden; }
        .pos-cats { display: flex; overflow-x: auto; padding-bottom: 5px; margin-bottom: 10px; }
        .pos-cats > * { margin-left: 8px; }
        .cat-btn { background: #333; color: #fff; padding: 8px 16px; border-radius: 50px; white-space: nowrap; border: 1px solid transparent; cursor: pointer; font-size: 0.9rem;}
        .cat-btn.active { background: var(--primary); color: #000; font-weight: bold; }
        .pos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(115px, 1fr)); gap: 12px; padding-bottom: 50px;}
        .mini-card { background: #252525; border-radius: 12px; padding: 10px; cursor: pointer; transition: 0.1s; border: 1px solid transparent; text-align: center; user-select: none; }
        .mini-card:hover { border-color: var(--primary); }
        .mini-card:active { transform: scale(0.95); background: #333; }
        .mini-card img { width: 55px; height: 55px; object-fit: contain; margin-bottom: 5px; }
        .mini-card h4 { font-size: 0.88rem; margin-bottom: 4px; height: 34px; overflow: hidden; line-height: 1.2; }
        .mini-card .price { color: var(--primary); font-size: 0.85rem; font-weight: bold; }
        .cart-header { padding: 12px; border-bottom: 1px solid #333; background: #2a2a2a; display: flex; justify-content: space-between; align-items: center; }
        .cart-items { flex: 1; overflow-y: auto; padding: 10px; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; background: #111; padding: 10px; border-radius: 10px; margin-bottom: 8px; border-right: 4px solid var(--primary); font-size: 0.9rem;}
        .cart-footer { padding: 12px; background: #2a2a2a; border-top: 1px solid #333; }
        .qty-ctrl { display: flex; align-items: center; background: #333; padding: 3px 8px; border-radius: 8px; }
        .qty-ctrl > * { margin: 0 5px; }
        
        .queue-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 15px; padding-top: 10px; }
        .order-ticket { background: #fff; color: #000; border-radius: 14px; padding: 16px; position: relative; border-top: 6px solid var(--warning); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .ticket-header { display: flex; justify-content: space-between; border-bottom: 2px dashed #ccc; padding-bottom: 12px; margin-bottom: 12px; align-items: center;}
        .ticket-items { font-family: 'Poppins', Tahoma, sans-serif; font-size: 0.9rem; margin-bottom: 15px; max-height: 220px; overflow-y: auto; }
        .ticket-row { display: flex; justify-content: space-between; margin-bottom: 6px; border-bottom: 1px solid #eee; padding-bottom: 4px;}
        .price-badge { background: #eee; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; color: #555; margin-left: 5px;}
        .ticket-actions { display: flex; gap: 8px; }
        
        /* Order Report Styles (بخش جدید گزارش سفارشات) */
        .report-filters { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; align-items: center; justify-content: space-between; background: #1a1a1a; padding: 16px; border-radius: 16px; border: 1px solid #333; }
        .filter-group { display: flex; flex-wrap: wrap; }
        .filter-group > * { margin-left: 8px; margin-bottom: 4px; }
        .filter-btn { background: #252525; color: #aaa; border: 1px solid #333; padding: 8px 16px; border-radius: 50px; cursor: pointer; font-size: 0.9rem; transition: 0.2s; }
        .filter-btn:hover { background: #333; color: #fff; }
        .filter-btn.active { background: var(--primary); color: #000; font-weight: bold; border-color: var(--primary); }
        .report-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(330px, 1fr)); gap: 16px; }
        .report-card { background: var(--bg-panel); border: 1px solid #333; border-radius: 16px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; transition: 0.2s; position: relative; }
        .report-card:hover { border-color: #555; box-shadow: 0 8px 25px rgba(0,0,0,0.4); }
        .report-card.completed { border-right: 6px solid var(--success); }
        .report-card.pending { border-right: 6px solid var(--warning); }
        .report-card.cancelled { border-right: 6px solid var(--danger); opacity: 0.85; }
        .report-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2a2a2a; padding-bottom: 10px; margin-bottom: 12px; }
        .report-badge { padding: 4px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: bold; }
        .report-badge.completed { background: rgba(46, 213, 115, 0.15); color: var(--success); }
        .report-badge.pending { background: rgba(255, 165, 2, 0.15); color: var(--warning); }
        .report-badge.cancelled { background: rgba(255, 71, 87, 0.15); color: var(--danger); }
        .report-items-list { background: #131313; border-radius: 12px; padding: 12px; margin-top: 12px; max-height: 160px; overflow-y: auto; font-size: 0.88rem; border: 1px solid #222; }
        .report-item-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed #252525; }
        .report-item-row:last-child { border-bottom: none; }
        .report-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #2a2a2a; padding-top: 12px; margin-top: 12px; font-size: 0.85rem; }

        .progress-bg { width:100%; background:#222; height:8px; border-radius:4px; overflow:hidden; }
        .progress-fill { height:100%; background:var(--primary); border-radius:4px; }
        
        @media(max-width:900px){ 
            .sidebar { transform:translateX(100%); width: 100%; max-width: 280px; box-shadow: -5px 0 20px rgba(0,0,0,0.5); } 
            .sidebar.show { transform:translateX(0); } 
            .main-content { margin:0; width:100%; padding: 12px; } 
            .menu-btn { display:block!important; } 
            .pos-container { grid-template-columns: 1fr; grid-template-rows: 1.5fr 1fr; gap: 8px; height: calc(100vh - 120px); }
            .pos-products { padding-bottom: 0; }
            .pos-cart { border-top: 2px solid var(--primary); box-shadow: 0 -5px 20px rgba(0,0,0,0.5); z-index: 10;}
        }
    </style>
</head>
<body>

<nav class="sidebar" id="sidebar">
    <div style="padding:1.5rem; text-align:center; border-bottom:1px solid #333; display:flex; justify-content:space-between; align-items:center">
        <h2 style="color:var(--primary)">کافه ریمچ</h2>
        <span style="cursor:pointer; color:#aaa; display:flex;" onclick="toggleSidebar()" title="بستن">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </span>
    </div>
    <div style="flex:1; padding:1rem 0; overflow-y: auto;">
        <div class="nav-item active" onclick="tab('dash', this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect></svg>
            داشبورد
        </div>
        <div class="nav-item" onclick="tab('pos', this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2"></path><rect x="9" y="11" width="12" height="10" rx="2"></rect><circle cx="15" cy="16" r="1"></circle></svg>
            صندوق (POS)
        </div>
        <div class="nav-item" onclick="tab('orders', this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1z"></path><line x1="8" y1="8" x2="16" y2="8"></line><line x1="8" y1="12" x2="16" y2="12"></line><line x1="8" y1="16" x2="12" y2="16"></line></svg>
            صف سفارشات
        </div>
        <!-- تب جدید: گزارش و تاریخچه سفارشات -->
        <div class="nav-item" onclick="tab('orders_report', this); loadOrdersReport();">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            گزارش سفارشات
        </div>
        <div class="nav-item" onclick="tab('prod_stats', this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            آمار محصولات
        </div>
        <div class="nav-item" onclick="tab('prod', this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"></path><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
            محصولات
        </div>
        <div class="nav-item" onclick="tab('cat', this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            دسته‌ها
        </div>
        <div class="nav-item" onclick="tab('coffee_lines', this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"></path><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"></path></svg>
            لاین‌های قهوه
        </div>
        <div class="nav-item" onclick="tab('vip_club', this); loadVipMembers();">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87"></path><path d="M16 3.13a4 4 0 010 7.75"></path></svg>
            باشگاه مشتریان (VIP)
        </div>
    </div>
    <div class="nav-item" style="color:var(--danger); margin:1rem" onclick="logout()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        خروج
    </div>
</nav>

<main class="main-content" id="main">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem">
        <span class="menu-btn" style="display:none; font-size:2rem; color:var(--primary); cursor:pointer" onclick="toggleSidebar()">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </span>
        <h4 style="color:#666">پنل مدیریت جامع کافه ریمچ</h4>
    </div>

    <!-- 1. داشبورد -->
    <section id="dash" class="view-section active">
        <h1>داشبورد مدیریت</h1>
        <h3 style="margin: 20px 0 10px; color: var(--primary)">خلاصه عملکرد فروش</h3>
        <div class="stat-grid">
            <div class="stat-card">
                <div><div class="stat-value" id="incDay">0 ت</div><div class="stat-label">فروش امروز</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="1.5" style="opacity:0.2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"></path></svg>
            </div>
            <div class="stat-card">
                <div><div class="stat-value" id="cntDay">0</div><div class="stat-label">تعداد سفارش امروز</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--info)" stroke-width="1.5" style="opacity:0.2"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1z"></path><line x1="8" y1="8" x2="16" y2="8"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </div>
            <div class="stat-card">
                <div><div class="stat-value" id="incWeek">0 ت</div><div class="stat-label">فروش هفته</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--info)" stroke-width="1.5" style="opacity:0.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div class="stat-card">
                <div><div class="stat-value" id="incMonth">0 ت</div><div class="stat-label">فروش ماه</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="1.5" style="opacity:0.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
        </div>
        <h3 style="margin: 20px 0 10px; color: var(--info)">وضعیت سیستم</h3>
        <div class="stat-grid">
            <div class="stat-card">
                <div><div class="stat-value" id="siteViews">0</div><div class="stat-label">بازدید کل سایت</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" style="opacity:0.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </div>
            <div class="stat-card">
                <div><h3 id="sTotal" style="font-size:1.7rem; color:#fff; margin-bottom:5px">0</h3><p class="stat-label">تعداد محصولات منو</p></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5" style="opacity:0.2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
        </div>
    </section>

    <!-- 2. آمار محصولات -->
    <section id="prod_stats" class="view-section">
        <h1>عملکرد محصولات</h1>
        <p style="color:#888; margin-bottom:20px">محصولاتی که بیشترین تعداد سفارش را داشته‌اند</p>
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th width="10%">رتبه</th>
                        <th>نام محصول</th>
                        <th>تعداد فروخته شده</th>
                        <th>مجموع درآمد</th>
                        <th>نمودار</th>
                    </tr>
                </thead>
                <tbody id="pStatsList"></tbody>
            </table>
        </div>
    </section>

    <!-- 3. سیستم POS -->
    <section id="pos" class="view-section">
        <div class="pos-container">
            <div class="pos-products">
                <div style="display:flex; margin-bottom:12px; position:relative;">
                    <input type="text" class="inp" placeholder="جستجوی سریع محصول..." style="margin:0; width:100%" oninput="posSearch(this.value)">
                </div>
                <div class="pos-cats" id="posCats">
                    <button class="cat-btn active" onclick="filterPos('all', this)">همه</button>
                </div>
                <div class="pos-grid" id="posGrid"></div>
            </div>
            <div class="pos-cart">
                <div class="cart-header">
                    <h4 style="font-size:1.1rem">سفارش جاری صندوق</h4>
                    <button class="btn-sm" style="color:var(--danger); background:transparent" onclick="clearCart()" title="پاک کردن سبد">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                </div>
                <div class="cart-items" id="cartItems">
                    <div style="text-align:center; color:#666; margin-top:30px; font-size:0.95rem">سبد سفارش خالی است</div>
                </div>
                <div class="cart-footer">
                    <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:1.15rem">
                        <span>مبلغ قابل پرداخت:</span>
                        <span style="color:var(--primary); font-weight:bold" id="cartTotal">0</span>
                    </div>
                    <button class="btn" style="width:100%; justify-content:center; padding:14px; font-size:1.15rem" onclick="submitOrder()">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        ثبت و ارسال به صف
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. صف سفارشات -->
    <section id="orders" class="view-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px">
            <h1>صف سفارشات جاری (در انتظار آماده‌سازی)</h1>
            <button class="btn-outline" onclick="loadOrders()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"></path></svg>
                بروزرسانی صف
            </button>
        </div>
        <div class="queue-grid" id="queueList"></div>
    </section>

    <!-- تب جدید: گزارش جامع و تاریخچه سفارشات -->
    <section id="orders_report" class="view-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px">
            <div>
                <h1 style="margin-bottom: 5px; color:var(--primary)">📊 گزارش و تاریخچه سفارشات صندوق</h1>
                <p style="color:#888; font-size:0.9rem">مشاهده جزئیات دقیق، ساعت ثبت، اقلام سفارشی و فاکتور تمامی سفارشات انجام‌شده</p>
            </div>
            <button class="btn-outline" onclick="loadOrdersReport()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"></path></svg>
                بروزرسانی گزارش
            </button>
        </div>

        <div class="stat-grid" style="margin-bottom: 20px;">
            <div class="stat-card">
                <div><div class="stat-value" id="repTotalCount">0</div><div class="stat-label">تعداد سفارشات در این فیلتر</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--info)" stroke-width="1.5" style="opacity:0.2"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1z"></path></svg>
            </div>
            <div class="stat-card">
                <div><div class="stat-value" id="repTotalRev">0 ت</div><div class="stat-label">مجموع فروش (سفارشات تکمیل‌شده)</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="1.5" style="opacity:0.2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"></path></svg>
            </div>
            <div class="stat-card">
                <div><div class="stat-value" id="repAvgRev">0 ت</div><div class="stat-label">میانگین مبلغ هر سفارش</div></div>
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="1.5" style="opacity:0.2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            </div>
        </div>

        <div class="report-filters" style="flex-direction: column; align-items: stretch; gap: 16px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div class="filter-group" id="statusFilterGroup">
                    <button class="filter-btn active" onclick="filterReportStatus('all', this)">همه وضعیت‌ها</button>
                    <button class="filter-btn" onclick="filterReportStatus('completed', this)">✅ تحویل‌شده (تکمیل)</button>
                    <button class="filter-btn" onclick="filterReportStatus('pending', this)">⏳ در انتظار آماده‌سازی</button>
                    <button class="filter-btn" onclick="filterReportStatus('cancelled', this)">❌ لغوشده</button>
                </div>
                <div class="filter-group" id="periodFilterGroup">
                    <button class="filter-btn active" onclick="filterReportPeriod('all', this)">کل تاریخچه</button>
                    <button class="filter-btn" onclick="filterReportPeriod('today', this)">امروز</button>
                    <button class="filter-btn" onclick="filterReportPeriod('week', this)">۷ روز گذشته</button>
                    <button class="filter-btn" onclick="filterReportPeriod('month', this)">۳۰ روز گذشته</button>
                </div>
            </div>

            <!-- فیلتر پیشرفته تاریخ دقیق و جستجو -->
            <div style="display: flex; flex-wrap: wrap; gap: 14px; border-top: 1px solid #282b24; padding-top: 15px; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="color:#a0a89a; font-size:0.92rem;">📅 فیلتر از تاریخ:</span>
                    <input type="date" id="repFromDate" class="inp" style="width: auto; margin: 0; padding: 7px 12px;">
                    <span style="color:#a0a89a; font-size:0.92rem;">تا تاریخ:</span>
                    <input type="date" id="repToDate" class="inp" style="width: auto; margin: 0; padding: 7px 12px;">
                    <button class="btn-sm" style="background: var(--primary); color: #000; font-weight: bold; padding: 7px 16px;" onclick="applyDateFilterReport()">اعمال تاریخ</button>
                    <button class="btn-sm" style="background: #2b3024; color: #fff; padding: 7px 14px;" onclick="resetDateFilterReport()">پاک کردن تاریخ</button>
                </div>
                
                <div style="position: relative; flex: 1; min-width: 250px; max-width: 380px;">
                    <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#666; display:flex;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" id="repSearchInput" class="inp" placeholder="جستجوی شماره فاکتور (#105) یا نام محصول..." 
                           style="margin:0; padding-left:38px; background:#111;" oninput="searchReportOrders(this.value)">
                </div>
            </div>
        </div>

        <div class="report-grid" id="ordersReportList"></div>
    </section>

    <!-- 5. محصولات -->
    <section id="prod" class="view-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px">
            <div style="display:flex; align-items:center; flex:1">
                <h1 style="margin-left: 15px;">محصولات</h1>
                <div style="position:relative; max-width:300px; width:100%;">
                    <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#666; display:flex;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" class="inp" placeholder="جستجوی نام محصول..." 
                           style="margin:0; padding-left:40px; background:#1e1e1e; border:1px solid #444; border-radius:50px;"
                           oninput="searchProducts(this.value)">
                </div>
            </div>
            <div style="display:flex;">
                <button class="btn-outline" style="margin-left:10px;" onclick="openBulk()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>
                    تخفیف کلی
                </button>
                <button class="btn" onclick="openPModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    جدید
                </button>
            </div>
        </div>
        <div class="table-box">
            <table>
                <thead><tr><th>نام</th><th>دسته‌بندی</th><th>توضیحات</th><th>قیمت</th><th>تخفیف</th><th>عملیات</th></tr></thead>
                <tbody id="list"></tbody>
            </table>
        </div>
    </section>

    <!-- 6. دسته‌ها -->
    <section id="cat" class="view-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px">
            <h1>دسته‌بندی‌ها</h1>
            <button class="btn" onclick="addCat()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                افزودن
            </button>
        </div>
        <p style="color:#888; margin-bottom:15px; font-size:0.9rem">برای تغییر ترتیب نمایش، از فلش‌های بالا و پایین استفاده کنید.</p>
        <div class="stat-grid" id="catList" style="grid-template-columns: 1fr;"></div>
    </section>

    <!-- لاین‌های قهوه -->
    <section id="coffee_lines" class="view-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px">
            <h1>مدیریت لاین‌های قهوه</h1>
            <button class="btn" onclick="openCLModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                افزودن لاین جدید
            </button>
        </div>
        <p style="color:#888; margin-bottom:15px; font-size:0.9rem">لاین‌های قهوه در صفحه اصلی و صفحه اختصاصی نمایش داده می‌شوند.</p>
        <div class="stat-grid" id="clList" style="grid-template-columns: 1fr;"></div>
    </section>

    <!-- بخش باشگاه مشتریان -->
    <section id="vip_club" class="view-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem">
            <h1 style="color: var(--primary)">لیست مشتریان ویژه (VIP)</h1>
            <button class="btn-outline" onclick="loadVipMembers()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"></path></svg>
                بروزرسانی لیست
            </button>
        </div>
        
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th style="text-align: center;">شماره تماس</th>
                        <th>تاریخ عضویت</th>
                    </tr>
                </thead>
                <tbody id="vipTableBody"></tbody>
            </table>
        </div>
    </section>
</main>

<!-- مودال محصول -->
<div class="modal-overlay" id="pModal">
    <div class="modal-box">
        <h3>مدیریت محصول</h3><br>
        <input type="text" id="mName" class="inp" placeholder="نام محصول">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px">
            <input type="text" id="mPrice" class="inp" placeholder="قیمت (تومان)" oninput="format(this)">
            <input type="number" id="mDisc" class="inp" placeholder="تخفیف %">
        </div>
        <label style="color:#aaa; display:block; margin:5px 0">محتویات / توضیحات:</label>
        <textarea id="mDesc" class="inp" placeholder="مواد تشکیل دهنده محصول..."></textarea>
        
        <label style="color:#aaa; display:block; margin-bottom:5px">دسته‌بندی‌ها:</label>
        <div id="mCats" style="display:grid; grid-template-columns:1fr 1fr; gap:10px; background:#111; padding:10px; border-radius:10px; margin-bottom:10px"></div>
        
        <input type="file" id="mFile" class="inp">
        <div style="text-align:left; margin-top:15px; display:flex; justify-content:flex-end; gap:8px;">
            <button class="btn" style="background:#333; color:#fff;" onclick="closeModal('pModal')">لغو</button>
            <button class="btn" onclick="saveP()">ذخیره</button>
        </div>
    </div>
</div>

<!-- مودال لاین قهوه -->
<div class="modal-overlay" id="clModal">
    <div class="modal-box">
        <h3>مدیریت لاین قهوه</h3><br>
        <input type="text" id="clName" class="inp" placeholder="نام لاین (مثال: لاین کلاسیک)">
        <input type="text" id="clSlug" class="inp" placeholder="اسلاگ (مثال: classic-line)">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px">
            <input type="text" id="clBlend" class="inp" placeholder="نسبت ترکیب (مثال: 50/50)">
            <input type="text" id="clBeans" class="inp" placeholder="نوع دانه (مثال: روبوستا و اربیکا)">
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px">
            <input type="text" id="clOrigin" class="inp" placeholder="خاستگاه (مثال: برزیل و کلمبیا)">
            <input type="text" id="clProcess" class="inp" placeholder="روش پردازش">
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px">
            <input type="text" id="clRoast" class="inp" placeholder="درجه برشته‌کاری">
            <input type="text" id="clFlavors" class="inp" placeholder="یادهای طعمی (با کاما جدا کنید)">
        </div>
        <input type="text" id="clShortDesc" class="inp" placeholder="توضیح کوتاه برای کارت">
        <textarea id="clDesc" class="inp" placeholder="توضیحات کامل..." style="min-height:100px"></textarea>
        <div style="text-align:left; margin-top:15px; display:flex; justify-content:flex-end; gap:8px;">
            <button class="btn" style="background:#333; color:#fff;" onclick="closeModal('clModal')">لغو</button>
            <button class="btn" onclick="saveCL()">ذخیره</button>
        </div>
    </div>
</div>

<script>
// --- آیکون‌های وکتور خالص برای جاوااسکریپت (بدون نیاز به فونت خارجی) ---
const SVG_ICONS = {
    close: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
    delete: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7 a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>',
    edit: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
    add: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
    remove: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
    done_all: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
    arrow_up: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg>',
    arrow_down: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>',
    time: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
};
function getSvg(name) { return SVG_ICONS[name] || ''; }

// --- GLOBAL AUTH & CONFIG ---
if(!localStorage.getItem('admin_in')) window.location.href='login.html';
function logout(){localStorage.removeItem('admin_in');window.location.href='login.html';}
let products=[], cats=[], cart=[], coffeeLines=[];
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('show'); }

// --- LOAD DATA ---
async function load(){
    try {
        const res = await fetch('api.php?action=get_data');
        const data = await res.json();
        products = data.products || []; 
        cats = (data.categories || []).sort((a,b) => (a.sort_order || 0) - (b.sort_order || 0));
        
        renderProducts();
        renderPOS();
        renderStats();
        loadOrders();
    } catch(e) { console.error(e); }
}

async function renderStats() {
    const sTotal = document.getElementById('sTotal');
    if (sTotal) sTotal.innerText = products.length;
    try {
        const res = await fetch('api.php?action=get_stats');
        const s = await res.json();
        
        const incDay = document.getElementById('incDay');
        if (incDay) incDay.innerText = parseInt(s.today.total).toLocaleString() + ' ت';
        const cntDay = document.getElementById('cntDay');
        if (cntDay) cntDay.innerText = s.today.count; 
        const incWeek = document.getElementById('incWeek');
        if (incWeek) incWeek.innerText = parseInt(s.week.total).toLocaleString() + ' ت';
        const incMonth = document.getElementById('incMonth');
        if (incMonth) incMonth.innerText = parseInt(s.month.total).toLocaleString() + ' ت';
        const siteViews = document.getElementById('siteViews');
        if (siteViews) siteViews.innerText = parseInt(s.views).toLocaleString();
    } catch(e){}
}

async function loadProductStats() {
    const list = document.getElementById('pStatsList');
    if (!list) return;
    list.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#aaa;">⏳ در حال دریافت آمار محصولات...</td></tr>';
    try {
        const res = await fetch('api.php?action=get_product_stats');
        const data = await res.json();
        
        if(!data || data.length === 0) {
            list.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">داده‌ای یافت نشد</td></tr>';
            return;
        }
        const maxQty = parseInt(data[0].qty);
        let html = '';
        data.forEach((item, index) => {
            const percent = maxQty > 0 ? (parseInt(item.qty) / maxQty) * 100 : 0;
            let badge = (index === 0) ? '🥇' : (index === 1) ? '🥈' : (index === 2) ? '🥉' : index + 1;
            html += `<tr><td style="font-size:1.2rem">${badge}</td><td>${item.name}</td><td style="color:#fff; font-weight:bold">${item.qty}</td><td style="color:var(--success)">${parseInt(item.total_rev).toLocaleString()} ت</td><td style="width:30%"><div class="progress-bg"><div class="progress-fill" style="width:${percent}%"></div></div></td></tr>`;
        });
        list.innerHTML = html;
    } catch(e) {
        list.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--danger);">خطا در بارگذاری آمار</td></tr>';
    }
}

function renderProducts(data=products){
    const list = document.getElementById('list');
    if (!list) return;
    let html = '';
    data.forEach(p => {
        let catDisplay = '';
        if(p.category_slug) {
            catDisplay = p.category_slug.split(',').map(slug => {
                let catName = cats.find(c => c.slug === slug.trim())?.name || slug;
                return `<span style="background:#222; padding:2px 6px; border-radius:4px; font-size:11px; margin-left:3px; color:#aaa">${catName}</span>`;
            }).join('');
        }
        html += `<tr>
            <td>
                <div class="td-flex">
                    <img src="${p.image}" style="width:38px; height:38px; border-radius:8px; object-fit:cover">
                    <span style="font-weight:bold">${p.name}</span>
                </div>
            </td>
            <td>${catDisplay}</td>
            <td><div class="desc-text" title="${p.description || ''}">${p.description || '-'}</div></td>
            <td style="font-weight:bold">${parseInt(p.price.replace(/,/g,'')).toLocaleString()}</td>
            <td><span style="color:var(--warning)">${p.discount}%</span></td>
            <td>
                <div style="display:flex; gap:6px;">
                    <button class="btn-sm" onclick="editP(${p.id})" title="ویرایش">${getSvg('edit')}</button>
                    <button class="btn-sm" style="color:var(--danger)" onclick="delP(${p.id})" title="حذف">${getSvg('delete')}</button>
                </div>
            </td>
        </tr>`;
    });
    list.innerHTML = html;
}

function searchProducts(query) {
    if(!query.trim()) { renderProducts(products); return; }
    const filtered = products.filter(p => p.name.includes(query) || (p.description && p.description.includes(query)));
    renderProducts(filtered);
}

// --- PRODUCTS MODAL & ACTIONS ---
let editId = null;
function openPModal() {
    editId = null;
    document.getElementById('mName').value = '';
    document.getElementById('mPrice').value = '';
    document.getElementById('mDisc').value = '0';
    document.getElementById('mDesc').value = '';
    document.getElementById('mFile').value = '';
    renderModalCats();
    document.getElementById('pModal').classList.add('open');
}

function renderModalCats(selectedSlugs = []) {
    const box = document.getElementById('mCats');
    if (!box) return;
    let html = '';
    cats.forEach(c => {
        const isChecked = selectedSlugs.includes(c.slug) ? 'checked' : '';
        html += `<label style="cursor:pointer; display:flex; align-items:center; gap:6px; font-size:0.9rem">
            <input type="checkbox" value="${c.slug}" class="cat-chk" ${isChecked}> ${c.name}
        </label>`;
    });
    box.innerHTML = html;
}

function editP(id) {
    editId = id;
    const p = products.find(x => x.id == id);
    document.getElementById('mName').value = p.name;
    document.getElementById('mPrice').value = parseInt(p.price.replace(/,/g,'')).toLocaleString();
    document.getElementById('mDisc').value = p.discount;
    document.getElementById('mDesc').value = p.description || '';
    const selected = p.category_slug ? p.category_slug.split(',').map(s=>s.trim()) : [];
    renderModalCats(selected);
    document.getElementById('pModal').classList.add('open');
}

function closeModal(id) { document.getElementById(id).classList.remove('open'); }

async function saveP() {
    const chks = document.querySelectorAll('.cat-chk:checked');
    const selectedCats = Array.from(chks).map(c => c.value).join(',');
    
    const fd = new FormData();
    fd.append('name', document.getElementById('mName').value);
    fd.append('price', document.getElementById('mPrice').value);
    fd.append('discount', document.getElementById('mDisc').value);
    fd.append('description', document.getElementById('mDesc').value);
    fd.append('category', selectedCats);
    if(editId) fd.append('id', editId);
    
    const file = document.getElementById('mFile').files[0];
    if(file) fd.append('image', file);
    
    await fetch('api.php?action=save_product', { method: 'POST', body: fd });
    closeModal('pModal'); load();
}

async function delP(id) {
    if(confirm('آیا از حذف این محصول اطمینان دارید؟')) {
        await fetch('api.php?action=delete_product', { method: 'POST', body: JSON.stringify({id: id}) });
        load();
    }
}

// --- CATEGORIES ---
function renderCategories() {
    const catList = document.getElementById('catList');
    if (!catList) return;
    if(cats.length === 0) {
        catList.innerHTML = '<div style="color:#666; text-align:center; padding:20px;">هیچ دسته‌بندی وجود ندارد.</div>';
        return;
    }

    let html = '';
    cats.forEach((c, index) => {
        const isFirst = index === 0;
        const isLast = index === cats.length - 1;

        html += `
        <div class="stat-card" style="padding:14px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center;">
                <div class="order-controls" style="margin-left:15px;">
                    <button class="order-btn" onclick="moveCat(${index}, -1)" ${isFirst ? 'disabled' : ''} title="حرکت به بالا">
                       ${getSvg('arrow_up')}
                    </button>
                    <span style="font-size:0.95rem; color:#888; width:22px; text-align:center;">${index + 1}</span>
                    <button class="order-btn" onclick="moveCat(${index}, 1)" ${isLast ? 'disabled' : ''} title="حرکت به پایین">
                       ${getSvg('arrow_down')}
                    </button>
                </div>
                <div>
                    <h4 style="margin:0; font-size:1.05rem;">${c.name}</h4>
                    <small style="color:#666">(${c.slug})</small>
                </div>
            </div>
            <button class="btn-sm" style="color:var(--danger); background:rgba(255, 71, 87, 0.1);" onclick="delCat(${c.id})">
                ${getSvg('delete')} حذف
            </button>
        </div>`; 
    });
    catList.innerHTML = html;
}

async function moveCat(index, direction) {
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= cats.length) return;
    
    const temp = cats[index];
    cats[index] = cats[targetIndex];
    cats[targetIndex] = temp;
    
    renderCategories();
    
    for (let i = 0; i < cats.length; i++) {
        cats[i].sort_order = i;
        await fetch('api.php?action=update_category_order', {
            method: 'POST',
            body: JSON.stringify({id: cats[i].id, order: i})
        });
    }
}

async function addCat() {
    const n = prompt('نام دسته‌بندی جدید (مثال: شیک‌ها):');
    if(n) {
        await fetch('api.php?action=add_category', { method: 'POST', body: JSON.stringify({name: n}) });
        load();
    }
}
async function delCat(id) {
    if(confirm('حذف دسته‌بندی؟')) {
        await fetch('api.php?action=delete_category', { method: 'POST', body: JSON.stringify({id: id}) });
        load();
    }
}

// --- POS ---
let posCat = 'all';
function renderPOS(data = products) {
    const box = document.getElementById('posGrid');
    const catsBox = document.getElementById('posCats');
    if (!box || !catsBox) return;
    
    let catsHtml = `<button class="cat-btn ${posCat==='all'?'active':''}" onclick="filterPos('all', this)">همه</button>`;
    cats.forEach(c => {
        catsHtml += `<button class="cat-btn ${posCat===c.slug?'active':''}" onclick="filterPos('${c.slug}', this)">${c.name}</button>`;
    });
    catsBox.innerHTML = catsHtml;
    
    let html = '';
    data.forEach(p => {
        if(posCat !== 'all' && (!p.category_slug || !p.category_slug.includes(posCat))) return;
        const finalPrice = parseInt(p.price.replace(/,/g,'')) * (1 - p.discount/100);
        html += `
        <div class="mini-card" onclick="addToCart(${p.id})">
            <img src="${p.image}">
            <h4>${p.name}</h4>
            <div class="price">${finalPrice.toLocaleString()}</div>
        </div>`;
    });
    box.innerHTML = html;
}
function filterPos(slug, btn) { posCat = slug; renderPOS(); }
function posSearch(val) {
    if(!val) renderPOS(products);
    else renderPOS(products.filter(p => p.name.includes(val)));
}

// --- CART ---
function addToCart(id) {
    const p = products.find(x => x.id == id);
    const finalPrice = parseInt(p.price.replace(/,/g,'')) * (1 - p.discount/100);
    const exist = cart.find(x => x.id == id);
    if(exist) exist.qty++;
    else cart.push({id: p.id, name: p.name, price: finalPrice, qty: 1});
    updateCartUI();
}
function updateCartUI() {
    const box = document.getElementById('cartItems');
    if (!box) return;
    let total = 0;
    let html = '';
    cart.forEach((c, idx) => {
        total += c.price * c.qty;
        html += `<div class="cart-item">
            <div style="flex:1">
                <div style="font-weight:bold; font-size:0.95rem">${c.name}</div>
                <small style="color:#888">${c.price.toLocaleString()} ت</small>
            </div>
            <div class="qty-ctrl">
                <span style="cursor:pointer; display:flex; color:var(--primary)" onclick="changeQty(${idx}, 1)">${getSvg('add')}</span>
                <span style="font-weight:bold; width:18px; text-align:center;">${c.qty}</span>
                <span style="cursor:pointer; display:flex; color:var(--danger)" onclick="changeQty(${idx}, -1)">${getSvg('remove')}</span>
            </div>
        </div>`;
    });
    if(cart.length === 0) {
        box.innerHTML = '<div style="text-align:center; color:#666; margin-top:40px; font-size:0.95rem">سبد سفارش خالی است</div>';
    } else {
        box.innerHTML = html;
    }
    const totalEl = document.getElementById('cartTotal');
    if (totalEl) totalEl.innerText = total.toLocaleString() + ' ت';
}
function changeQty(idx, delta) { cart[idx].qty += delta; if(cart[idx].qty <= 0) cart.splice(idx, 1); updateCartUI(); }
function clearCart() { cart = []; updateCartUI(); }

async function submitOrder() {
    if(cart.length === 0) return alert('سبد خالی است');
    const total = cart.reduce((sum, c) => sum + (c.price * c.qty), 0);
    if(confirm(`ثبت سفارش در صندوق؟\nمبلغ کل: ${total.toLocaleString()} تومان`)) {
        const res = await fetch('api.php?action=create_order', { method: 'POST', body: JSON.stringify({ items: cart, total: total }) });
        const d = await res.json();
        if(d.status === 'success') { 
            clearCart(); 
            loadOrders(); 
            renderStats(); 
            if (typeof loadOrdersReport === 'function') loadOrdersReport();
        } else { 
            alert('خطا: ' + d.message); 
        }
    }
}

// --- ORDERS QUEUE ---
async function loadOrders() {
    const box = document.getElementById('queueList');
    if (!box) return;
    try {
        const res = await fetch('api.php?action=get_orders');
        const data = await res.json();
        if(!data.pending || data.pending.length === 0) { box.innerHTML = '<h3 style="color:#666; text-align:center; grid-column:1/-1; padding:40px;">سفارشی در صف آماده‌سازی نیست</h3>'; return; }
        let html = '';
        data.pending.forEach(ord => {
            let itemsHtml = ''; let ordTotal = parseInt(ord.total_price);
            ord.items.forEach(i => {
                let itemTotal = i.price_each * i.quantity;
                itemsHtml += `<div class="ticket-row">
                    <div style="flex:1">
                        <span style="font-weight:bold">${i.product_name}</span>
                        <span class="price-badge">${parseInt(i.price_each).toLocaleString()}</span>
                        <small style="color:#888">x ${i.quantity}</small>
                    </div>
                    <div style="align-self:center; font-weight:bold">${itemTotal.toLocaleString()} ت</div>
                </div>`;
            });
            let timeFormatted = '-';
            try { timeFormatted = new Date(ord.created_at).toLocaleTimeString('fa-IR', {hour:'2-digit', minute:'2-digit'}); } catch(e){}
            html += `
            <div class="order-ticket">
                <div class="ticket-header">
                    <div><strong style="font-size:1.1rem">سفارش #${ord.id}</strong><br><small style="color:#666; display:flex; align-items:center; gap:4px; margin-top:2px;">${getSvg('time')} ${timeFormatted}</small></div>
                    <div style="text-align:left"><div style="font-size:0.8rem; color:#888">مجموع:</div><div style="font-size:1.2rem; font-weight:bold; color:var(--primary)">${ordTotal.toLocaleString()} ت</div></div>
                </div>
                <div class="ticket-items">${itemsHtml}</div>
                <div class="ticket-actions">
                    <button class="btn-sm" style="background:var(--danger); width:44px;" onclick="cancelOrder(${ord.id})" title="لغو سفارش">
                        ${getSvg('close')}
                    </button>
                    <button class="btn" style="flex:1; justify-content:center" onclick="completeOrder(${ord.id})">
                        ${getSvg('done_all')} تحویل شد
                    </button>
                </div>
            </div>`;
        });
        box.innerHTML = html;
    } catch(e){}
}
async function completeOrder(id) { 
    if(confirm('سفارش تحویل داده شد؟')) { 
        await fetch('api.php?action=complete_order', { method: 'POST', body: JSON.stringify({id: id}) }); 
        loadOrders(); renderStats(); 
        if (typeof loadOrdersReport === 'function') loadOrdersReport();
    } 
}
async function cancelOrder(id) { 
    if(confirm('آیا مطمئنید می‌خواهید این سفارش را لغو کنید؟')) { 
        await fetch('api.php?action=cancel_order', { method: 'POST', body: JSON.stringify({id: id}) }); 
        loadOrders(); renderStats();
        if (typeof loadOrdersReport === 'function') loadOrdersReport();
    } 
}

// ==========================================
// بخش جدید: گزارش و تاریخچه سفارشات
// ==========================================
let currentReportStatus = 'all';
let currentReportPeriod = 'all';
let allReportOrders = [];

function filterReportStatus(status, btn) {
    currentReportStatus = status;
    document.querySelectorAll('#statusFilterGroup .filter-btn').forEach(b => b.classList.remove('active'));
    if(btn) btn.classList.add('active');
    loadOrdersReport();
}
function filterReportPeriod(period, btn) {
    currentReportPeriod = period;
    document.querySelectorAll('#periodFilterGroup .filter-btn').forEach(b => b.classList.remove('active'));
    if(btn) btn.classList.add('active');
    loadOrdersReport();
}

function formatDateTimePersian(dateStr) {
    if(!dateStr) return '-';
    try {
        const d = new Date(dateStr);
        if (typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
            const datePart = new Intl.DateTimeFormat('fa-IR', { calendar: 'persian', year: 'numeric', month: '2-digit', day: '2-digit' }).format(d);
            const timePart = new Intl.DateTimeFormat('fa-IR', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(d);
            return `${datePart} - ساعت ${timePart}`;
        }
        return d.toLocaleString('fa-IR');
    } catch(e) { return dateStr; }
}

async function loadOrdersReport() {
    const box = document.getElementById('ordersReportList');
    if(!box) return;
    box.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px; color:#aaa;">⏳ در حال دریافت گزارش تاریخچه سفارشات...</div>';
    try {
        let fromDate = document.getElementById('repFromDate') ? document.getElementById('repFromDate').value : '';
        let toDate = document.getElementById('repToDate') ? document.getElementById('repToDate').value : '';
        const res = await fetch(`api.php?action=get_orders_report&status=${currentReportStatus}&period=${currentReportPeriod}&from_date=${fromDate}&to_date=${toDate}`);
        const data = await res.json();
        allReportOrders = data.orders || [];
        
        let searchInput = document.getElementById('repSearchInput');
        if (searchInput && searchInput.value.trim()) {
            searchReportOrders(searchInput.value);
        } else {
            renderOrdersReport(allReportOrders);
        }
    } catch(e) {
        box.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px; color:var(--danger);">❌ خطا در بارگذاری گزارش سفارشات</div>';
    }
}

function applyDateFilterReport() {
    document.querySelectorAll('#periodFilterGroup .filter-btn').forEach(b => b.classList.remove('active'));
    loadOrdersReport();
}

function resetDateFilterReport() {
    if (document.getElementById('repFromDate')) document.getElementById('repFromDate').value = '';
    if (document.getElementById('repToDate')) document.getElementById('repToDate').value = '';
    if (document.getElementById('repSearchInput')) document.getElementById('repSearchInput').value = '';
    filterReportPeriod('all', document.querySelector('#periodFilterGroup .filter-btn'));
}

function searchReportOrders(val) {
    if (!val || !val.trim()) {
        renderOrdersReport(allReportOrders);
        return;
    }
    const q = val.trim().toLowerCase();
    const filtered = allReportOrders.filter(ord => {
        if (ord.id.toString().includes(q)) return true;
        if (ord.items && ord.items.some(it => it.product_name.toLowerCase().includes(q))) return true;
        if (ord.total_price.toString().includes(q)) return true;
        return false;
    });
    renderOrdersReport(filtered);
}

function renderOrdersReport(ordersList = allReportOrders) {
    const box = document.getElementById('ordersReportList');
    if(!box) return;
    
    const totalCount = ordersList.length;
    let completedCount = 0;
    let totalRevenue = 0;
    
    ordersList.forEach(ord => {
        if (ord.status === 'completed') {
            completedCount++;
            totalRevenue += parseInt(ord.total_price || 0);
        }
    });
    const avgRev = completedCount > 0 ? Math.round(totalRevenue / completedCount) : 0;
    
    document.getElementById('repTotalCount').innerText = totalCount.toLocaleString('fa-IR');
    document.getElementById('repTotalRev').innerText = totalRevenue.toLocaleString('fa-IR') + ' ت';
    document.getElementById('repAvgRev').innerText = avgRev.toLocaleString('fa-IR') + ' ت';
    
    if (totalCount === 0) {
        box.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 50px; background:var(--bg-panel); border-radius:16px; color:#888;">هیچ سفارشی با این مشخصات یافت نشد.</div>';
        return;
    }
    
    let html = '';
    ordersList.forEach(ord => {
        let statusClass = ord.status;
        let statusText = ord.status === 'completed' ? '✅ تحویل‌شده' : (ord.status === 'pending' ? '⏳ در انتظار' : '❌ لغوشده');
        let formattedTime = formatDateTimePersian(ord.created_at);
        
        let itemsHtml = '';
        if (ord.items && ord.items.length > 0) {
            ord.items.forEach(it => {
                let itemTotal = parseInt(it.quantity) * parseInt(it.price_each);
                itemsHtml += `
                    <div class="report-item-row">
                        <span><b style="color:var(--primary); font-size:0.95rem;">${it.quantity}x</b> ${it.product_name}</span>
                        <span style="color:#bbb; font-weight:bold;">${itemTotal.toLocaleString()} ت</span>
                    </div>
                `;
            });
        } else {
            itemsHtml = '<div style="color:#666; text-align:center;">جزئیات اقلام موجود نیست</div>';
        }
        
        html += `
            <div class="report-card ${statusClass}">
                <div>
                    <div class="report-header">
                        <span style="font-size:1.15rem; font-weight:bold; color:#fff;">فاکتور #${ord.id}</span>
                        <span class="report-badge ${statusClass}">${statusText}</span>
                    </div>
                    <div style="color:#aaa; font-size:0.85rem; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                        ${getSvg('time')} <span>${formattedTime}</span>
                    </div>
                    <div style="font-size:1.1rem; font-weight:bold; color:var(--primary); margin: 8px 0;">
                        مبلغ نهایی: ${parseInt(ord.total_price).toLocaleString()} تومان
                    </div>
                    <div class="report-items-list">
                        ${itemsHtml}
                    </div>
                </div>
                <div class="report-footer">
                    <span style="color:#888; font-size:0.85rem;">اقلام: ${ord.items_count || (ord.items ? ord.items.length : 0)} عدد</span>
                    <div style="display:flex; gap:6px;">
                        ${ord.status !== 'completed' ? `<button class="btn-sm" style="background:var(--success); color:#000; font-size:0.8rem; font-weight:bold; padding:5px 10px;" onclick="changeOrderStatusReport(${ord.id}, 'completed')">${getSvg('done_all')} تحویل</button>` : ''}
                        ${ord.status !== 'cancelled' ? `<button class="btn-sm" style="background:var(--danger); color:#fff; font-size:0.8rem; padding:5px 10px;" onclick="changeOrderStatusReport(${ord.id}, 'cancelled')">${getSvg('close')} لغو</button>` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    box.innerHTML = html;
}

async function changeOrderStatusReport(id, newStatus) {
    let action = newStatus === 'completed' ? 'complete_order' : 'cancel_order';
    await fetch(`api.php?action=${action}`, { method: 'POST', body: JSON.stringify({ id: id }) });
    loadOrdersReport();
    loadOrders();
    renderStats();
}

async function openBulk() {
    const p = prompt('درصد تخفیف را وارد کنید (برای حذف همه تخفیف‌ها 0 وارد کنید):');
    if(p !== null) {
        if(isNaN(p) || p < 0 || p > 100) return alert('عدد نامعتبر');
        await fetch('api.php?action=bulk_discount', {
            method: 'POST',
            body: JSON.stringify({percent: p})
        });
        load();
    }
}

function format(input) {
    let val = input.value.replace(/\D/g, '');
    input.value = val ? parseInt(val).toLocaleString() : '';
}

// --- COFFEE LINES MANAGEMENT ---
async function loadCoffeeLines() {
    try {
        const res = await fetch('api.php?action=get_all_coffee_lines');
        const data = await res.json();
        coffeeLines = data.lines || [];
        renderCoffeeLines();
    } catch (e) { console.error('Error loading coffee lines:', e); }
}

function renderCoffeeLines() {
    const clList = document.getElementById('clList');
    if(!clList) return;
    if(coffeeLines.length === 0) {
        clList.innerHTML = '<div style="color:#666; text-align:center; padding:20px;">هیچ لاین قهوه‌ای وجود ندارد.</div>';
        return;
    }

    let html = '';
    coffeeLines.forEach((cl, index) => {
        const isFirst = index === 0;
        const isLast = index === coffeeLines.length - 1;
        html += `
        <div class="stat-card" style="padding:15px; display:flex; justify-content:space-between; align-items:center; ${cl.is_active == 0 ? 'opacity:0.5' : ''}">
            <div style="display:flex; align-items:center; flex:1">
                <div class="order-controls" style="margin-left:15px;">
                    <button class="order-btn" onclick="moveCL(${index}, -1)" ${isFirst ? 'disabled' : ''} title="بالا">
                       ${getSvg('arrow_up')}
                    </button>
                    <span style="font-size:0.9rem; color:#888; width:22px; text-align:center;">${index + 1}</span>
                    <button class="order-btn" onclick="moveCL(${index}, 1)" ${isLast ? 'disabled' : ''} title="پایین">
                       ${getSvg('arrow_down')}
                    </button>
                </div>
                <img src="${cl.image || 'image/cafe.png'}" style="width:50px; height:50px; border-radius:10px; object-fit:cover; margin-left:12px;">
                <div style="flex:1">
                    <h4 style="margin:0; font-size:1.05rem; ${cl.is_active == 0 ? 'text-decoration:line-through' : ''}">${cl.name}</h4>
                    <small style="color:#888">${cl.blend_ratio || ''} | ${cl.beans_type || ''}</small>
                    ${cl.is_active == 0 ? '<span style="color:#ff4757; font-size:0.8rem; margin-right:10px;">(غیرفعال)</span>' : ''}
                </div>
            </div>
            <div style="display:flex; gap:6px;">
                <button class="btn-sm" onclick="editCL(${cl.id})" title="ویرایش">${getSvg('edit')}</button>
                <button class="btn-sm" style="color:${cl.is_active == 1 ? 'var(--warning)' : 'var(--success)'}; font-size:0.85rem; padding:6px 12px;" onclick="toggleCL(${cl.id}, ${cl.is_active == 1 ? 0 : 1})" title="${cl.is_active == 1 ? 'غیرفعال' : 'فعال'} کردن">
                    ${cl.is_active == 1 ? 'غیرفعال' : 'فعال'}
                </button>
                <button class="btn-sm" style="color:var(--danger)" onclick="deleteCL(${cl.id})" title="حذف">${getSvg('delete')}</button>
            </div>
        </div>`;
    });
    clList.innerHTML = html;
}

async function moveCL(index, direction) {
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= coffeeLines.length) return;
    
    const temp = coffeeLines[index];
    coffeeLines[index] = coffeeLines[targetIndex];
    coffeeLines[targetIndex] = temp;
    
    renderCoffeeLines();
    
    for (let i = 0; i < coffeeLines.length; i++) {
        coffeeLines[i].sort_order = i;
        await fetch('api.php?action=update_coffee_line_order', {
            method: 'POST',
            body: JSON.stringify({id: coffeeLines[i].id, order: i})
        });
    }
}

let clEditId = null;
function openCLModal() {
    clEditId = null;
    document.getElementById('clName').value = '';
    document.getElementById('clSlug').value = '';
    document.getElementById('clBlend').value = '';
    document.getElementById('clBeans').value = '';
    document.getElementById('clOrigin').value = '';
    document.getElementById('clProcess').value = '';
    document.getElementById('clRoast').value = '';
    document.getElementById('clFlavors').value = '';
    document.getElementById('clShortDesc').value = '';
    document.getElementById('clDesc').value = '';
    document.getElementById('clModal').classList.add('open');
}

function editCL(id) {
    clEditId = id;
    const cl = coffeeLines.find(x => x.id == id);
    document.getElementById('clName').value = cl.name;
    document.getElementById('clSlug').value = cl.slug;
    document.getElementById('clBlend').value = cl.blend_ratio || '';
    document.getElementById('clBeans').value = cl.beans_type || '';
    document.getElementById('clOrigin').value = cl.origin || '';
    document.getElementById('clProcess').value = cl.process || '';
    document.getElementById('clRoast').value = cl.roast_level || '';
    document.getElementById('clFlavors').value = cl.flavor_notes || '';
    document.getElementById('clShortDesc').value = cl.short_desc || '';
    document.getElementById('clDesc').value = cl.description || '';
    document.getElementById('clModal').classList.add('open');
}

async function saveCL() {
    const data = {
        id: clEditId,
        name: document.getElementById('clName').value,
        slug: document.getElementById('clSlug').value,
        blend_ratio: document.getElementById('clBlend').value,
        beans_type: document.getElementById('clBeans').value,
        origin: document.getElementById('clOrigin').value,
        process: document.getElementById('clProcess').value,
        roast_level: document.getElementById('clRoast').value,
        flavor_notes: document.getElementById('clFlavors').value,
        short_desc: document.getElementById('clShortDesc').value,
        description: document.getElementById('clDesc').value
    };
    
    await fetch('api.php?action=save_coffee_line', {
        method: 'POST',
        body: JSON.stringify(data)
    });
    
    closeModal('clModal');
    loadCoffeeLines();
}

async function toggleCL(id, newStatus) {
    const cl = coffeeLines.find(x => x.id == id);
    cl.is_active = newStatus;
    await fetch('api.php?action=save_coffee_line', {
        method: 'POST',
        body: JSON.stringify(cl)
    });
    loadCoffeeLines();
}

async function deleteCL(id) {
    if (confirm('حذف این لاین؟')) {
        await fetch('api.php?action=delete_coffee_line', {
            method: 'POST',
            body: JSON.stringify({id: id})
        });
        loadCoffeeLines();
    }
}

// --- VIP CLUB MANAGEMENT ---
async function loadVipMembers() {
    const tbody = document.getElementById('vipTableBody');
    if(!tbody) return;
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">در حال بارگذاری...</td></tr>';

    try {
        const res = await fetch('api.php?action=get_vip_members');
        const data = await res.json();
        
        if (data.members && data.members.length > 0) {
            let html = '';
            data.members.forEach(m => {
                let formattedDate = '-';
                try {
                    const dateObj = new Date(m.created_at || m.joined_at);
                    if (typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
                        formattedDate = new Intl.DateTimeFormat('fa-IR', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute:'2-digit' }).format(dateObj);
                    } else {
                        formattedDate = dateObj.toLocaleDateString('fa-IR') + " - " + dateObj.toLocaleTimeString('fa-IR', {hour: '2-digit', minute:'2-digit'});
                    }
                } catch(e){}

                html += `
                <tr>
                    <td>#${m.id}</td>
                    <td>${m.first_name}</td>
                    <td>${m.last_name}</td>
                    <td style="direction:ltr; text-align:center; font-family: monospace; font-size:1.1rem">${m.phone}</td>
                    <td style="color: #aaa;">${formattedDate}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">هیچ عضوی در باشگاه مشتریان یافت نشد.</td></tr>';
        }
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--danger);">خطا در دریافت اطلاعات از سرور</td></tr>';
    }
}

// --- NAVIGATION ---
function tab(id, el) {
    document.querySelectorAll('.view-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    const target = document.getElementById(id);
    if (target) target.classList.add('active');
    if (el) el.classList.add('active');
    
    if (id === 'prod') renderProducts();
    if (id === 'cat') renderCategories();
    if (id === 'coffee_lines') loadCoffeeLines();
    if (id === 'orders') loadOrders();
    if (id === 'orders_report') loadOrdersReport();
    if (id === 'prod_stats') loadProductStats();
    if (id === 'vip_club') loadVipMembers();
    
    if (window.innerWidth <= 900) {
        document.getElementById('sidebar').classList.remove('show');
    }
}

// --- INIT ---
load();
</script></body></html>