@extends('layouts.app')
@section('title','Dashboard Keuangan')
@section('header','Dashboard Keuangan')
@section('subheader','Analitik interaktif dengan filter periode')
@section('content')
<div x-data="ownerDash()" x-init="init()" class="space-y-6">

    <!-- Filter -->
    <div class="card p-5">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <button @click="setPreset('today')" class="btn-secondary btn-sm" data-testid="preset-today">Hari Ini</button>
            <button @click="setPreset('yesterday')" class="btn-secondary btn-sm">Kemarin</button>
            <button @click="setPreset('7d')" class="btn-secondary btn-sm">7 Hari</button>
            <button @click="setPreset('30d')" class="btn-secondary btn-sm" data-testid="preset-30d">30 Hari</button>
            <button @click="setPreset('this_month')" class="btn-secondary btn-sm">Bulan Ini</button>
            <button @click="setPreset('last_month')" class="btn-secondary btn-sm">Bulan Lalu</button>
            <button @click="setPreset('this_year')" class="btn-secondary btn-sm">Tahun Ini</button>
        </div>
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[180px]">
                <label class="label">Dari</label>
                <input type="text" id="from-picker" class="input" data-testid="from-date">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="label">Sampai</label>
                <input type="text" id="to-picker" class="input" data-testid="to-date">
            </div>
            <button @click="fetchData()" class="btn-primary" data-testid="apply-filter">🔍 Terapkan Filter</button>
            <div class="flex gap-1">
                <a :href="exportUrl('pdf')" target="_blank" class="btn-ghost btn-sm" data-testid="export-pdf">📄 PDF</a>
                <a :href="exportUrl('excel')" class="btn-ghost btn-sm">📊 Excel</a>
                <a :href="exportUrl('csv')" class="btn-ghost btn-sm">📃 CSV</a>
            </div>
        </div>
        <div x-show="loading" class="mt-3 text-coffee-500 text-sm flex items-center gap-2">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-30"></circle><path d="M22 12c0-5.5-4.5-10-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path></svg>
            Memuat data...
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4" data-testid="kpi-cards">
        <div class="card p-5">
            <div class="text-xs uppercase tracking-wider text-coffee-500">💰 Total Revenue</div>
            <div class="font-mono-num text-3xl font-bold mt-1 text-caramel-600" x-text="rupiah(kpi.revenue || 0)" data-testid="kpi-revenue"></div>
            <div class="text-xs mt-1" :class="(kpi.delta_revenue||0)>=0 ? 'text-emerald-500' : 'text-red-500'">
                <span x-text="(kpi.delta_revenue||0)>=0 ? '▲' : '▼'"></span>
                <span x-text="Math.abs(kpi.delta_revenue||0) + '%'"></span>
                vs periode lalu
            </div>
        </div>
        <div class="card p-5">
            <div class="text-xs uppercase tracking-wider text-coffee-500">🧾 Transaksi</div>
            <div class="font-mono-num text-3xl font-bold mt-1" x-text="(kpi.orders||0).toLocaleString('id-ID')"></div>
            <div class="text-xs mt-1" :class="(kpi.delta_orders||0)>=0 ? 'text-emerald-500' : 'text-red-500'">
                <span x-text="(kpi.delta_orders||0)>=0 ? '▲' : '▼'"></span>
                <span x-text="Math.abs(kpi.delta_orders||0) + '%'"></span> vs periode lalu
            </div>
        </div>
        <div class="card p-5">
            <div class="text-xs uppercase tracking-wider text-coffee-500">📊 AOV</div>
            <div class="font-mono-num text-3xl font-bold mt-1" x-text="rupiah(kpi.aov || 0)"></div>
            <div class="text-xs mt-1" :class="(kpi.delta_aov||0)>=0 ? 'text-emerald-500' : 'text-red-500'">
                <span x-text="(kpi.delta_aov||0)>=0 ? '▲' : '▼'"></span>
                <span x-text="Math.abs(kpi.delta_aov||0) + '%'"></span> vs periode lalu
            </div>
        </div>
        <div class="card p-5">
            <div class="text-xs uppercase tracking-wider text-coffee-500">⚠️ Stok Kritis</div>
            <div class="font-mono-num text-3xl font-bold mt-1" :class="(kpi.critical_stock||0)>0?'text-red-500':''" x-text="kpi.critical_stock || 0"></div>
            <div class="text-xs text-coffee-500 mt-1">Perlu restock segera</div>
        </div>
    </div>

    <!-- Payment Breakdown -->
    <div class="card p-5">
        <div class="font-display text-lg font-semibold mb-4">💳 Breakdown Metode Pembayaran</div>
        <template x-for="p in payments" :key="p.method">
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold capitalize">
                        <span x-text="p.method==='cash'?'💵 Cash':p.method==='transfer'?'🏦 Transfer':'📱 QRIS'"></span>
                    </span>
                    <span class="font-mono-num">
                        <span x-text="rupiah(p.total)"></span>
                        <span class="text-coffee-500 ml-2" x-text="p.percent + '%'"></span>
                    </span>
                </div>
                <div class="h-2.5 rounded-full bg-coffee-100 dark:bg-coffee-700 overflow-hidden">
                    <div class="h-full bg-caramel-500 transition-all duration-700" :style="`width: ${p.percent}%`"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Revenue Daily Chart -->
    <div class="card p-5">
        <div class="flex justify-between items-center mb-4">
            <div class="font-display text-lg font-semibold">📈 Revenue Harian</div>
            <div class="flex gap-1">
                <button @click="setChartType('line')" :class="chartType==='line'?'btn-primary':'btn-ghost'" class="btn-sm">Line</button>
                <button @click="setChartType('bar')" :class="chartType==='bar'?'btn-primary':'btn-ghost'" class="btn-sm">Bar</button>
                <button @click="setChartType('area')" :class="chartType==='area'?'btn-primary':'btn-ghost'" class="btn-sm">Area</button>
            </div>
        </div>
        <div class="h-72"><canvas id="chart-revenue"></canvas></div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">
        <div class="card p-5">
            <div class="font-display text-lg font-semibold mb-4">🏆 Top 10 Menu Terlaris</div>
            <div class="h-80"><canvas id="chart-top"></canvas></div>
        </div>
        <div class="card p-5">
            <div class="font-display text-lg font-semibold mb-4">🥧 Revenue per Kategori</div>
            <div class="h-80"><canvas id="chart-cat"></canvas></div>
        </div>
    </div>

    <!-- Heatmap -->
    <div class="card p-5 overflow-x-auto">
        <div class="font-display text-lg font-semibold mb-4">🕐 Peak Hours (Jam × Hari)</div>
        <table class="text-xs font-mono-num">
            <thead><tr><th></th>
                <template x-for="d in days" :key="d.dow"><th class="px-2 pb-1 text-center" x-text="d.label"></th></template>
            </tr></thead>
            <tbody>
                <template x-for="h in hours" :key="h">
                    <tr>
                        <td class="pr-2 text-right text-coffee-500" x-text="String(h).padStart(2,'0')+':00'"></td>
                        <template x-for="d in days" :key="d.dow">
                            <td class="p-0.5">
                                <div class="w-8 h-7 rounded flex items-center justify-center text-[10px]"
                                     :class="`heat-${getHeatLevel(h, d.dow)}`"
                                     :title="`${d.label} ${h}:00 — ${getHeatCount(h, d.dow)} order`">
                                    <span x-text="getHeatCount(h, d.dow) || ''"></span>
                                </div>
                            </td>
                        </template>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Low Stock -->
    <div class="card p-5">
        <div class="font-display text-lg font-semibold mb-3">⚠️ Monitor Stok Menu (view only)</div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <template x-for="m in lowStock" :key="m.id">
                <div class="p-3 rounded-xl bg-coffee-50 dark:bg-coffee-700/40 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-sm" x-text="m.name"></div>
                        <div class="text-xs text-coffee-500">Min: <span x-text="m.alert_min"></span></div>
                    </div>
                    <div class="text-right">
                        <div class="font-mono-num font-bold" :class="m.status==='habis'?'text-red-500':'text-amber-500'" x-text="m.stock_qty"></div>
                        <div class="text-[10px] uppercase font-semibold" x-text="m.status"></div>
                    </div>
                </div>
            </template>
            <div x-show="lowStock.length===0" class="text-coffee-500 text-sm col-span-full">Semua stok aman ✓</div>
        </div>
    </div>
</div>

<script>
function ownerDash() {
    // Instance Chart disimpan di luar state Alpine supaya tidak dibungkus Proxy
    const charts = { revenue: null, top: null, cat: null };

    return {
        from: @json($from),
        to: @json($to),
        loading: false,
        kpi: {},
        payments: [],
        daily: [],
        topMenus: [],
        byCategory: [],
        heatmap: [],
        lowStock: [],
        chartType: 'line',
        days: [
            {dow:2,label:'Sen'},{dow:3,label:'Sel'},{dow:4,label:'Rab'},{dow:5,label:'Kam'},{dow:6,label:'Jum'},{dow:7,label:'Sab'},{dow:1,label:'Min'},
        ],
        hours: Array.from({length: 15}, (_,i) => i+8),

        init() {
            const fp1 = flatpickr('#from-picker', { dateFormat:'Y-m-d', locale: window.flatpickrLocale, defaultDate: this.from });
            const fp2 = flatpickr('#to-picker',   { dateFormat:'Y-m-d', locale: window.flatpickrLocale, defaultDate: this.to });
            this.fp1 = fp1; this.fp2 = fp2;
            fp1.config.onChange.push((d)=>{ this.from = fp1.input.value; });
            fp2.config.onChange.push((d)=>{ this.to = fp2.input.value; });
            this.fetchData();
        },

        setPreset(p) {
            const today = new Date();
            const fmt = (d) => d.toISOString().slice(0,10);
            let f = new Date(today), t = new Date(today);
            if (p==='today') { /* same */ }
            else if (p==='yesterday') { f.setDate(today.getDate()-1); t.setDate(today.getDate()-1); }
            else if (p==='7d') { f.setDate(today.getDate()-6); }
            else if (p==='30d') { f.setDate(today.getDate()-29); }
            else if (p==='this_month') { f.setDate(1); }
            else if (p==='last_month') { f = new Date(today.getFullYear(), today.getMonth()-1, 1); t = new Date(today.getFullYear(), today.getMonth(), 0); }
            else if (p==='this_year') { f = new Date(today.getFullYear(), 0, 1); }
            this.from = fmt(f); this.to = fmt(t);
            this.fp1.setDate(this.from); this.fp2.setDate(this.to);
            this.fetchData();
        },

        async fetchData() {
            this.loading = true;
            const url = `{{ route('owner.dashboard.data') }}?from=${this.from}&to=${this.to}`;
            try {
                const r = await fetch(url);
                const j = await r.json();
                this.kpi = j.kpi || {};
                this.payments = j.payments || [];
                this.daily = j.daily || [];
                this.topMenus = j.top_menus || [];
                this.byCategory = j.by_category || [];
                this.heatmap = j.heatmap || [];
                this.lowStock = j.low_stock || [];
                history.replaceState(null,'',`?from=${this.from}&to=${this.to}`);
                this.renderCharts();
            } catch(e) { console.error(e); window.toast('Gagal memuat data','error'); }
            this.loading = false;
        },

        renderCharts() {
            // Revenue
            const ctxR = document.getElementById('chart-revenue').getContext('2d');
            if (charts.revenue) charts.revenue.destroy();
            const isArea = this.chartType==='area';
            charts.revenue = new Chart(ctxR, {
                type: this.chartType==='bar' ? 'bar' : 'line',
                data: {
                    labels: this.daily.map(d=>d.label),
                    datasets: [{
                        label: 'Revenue',
                        data: this.daily.map(d=>d.total),
                        borderColor: '#C4813D',
                        backgroundColor: isArea ? 'rgba(196,129,61,0.25)' : (this.chartType==='bar' ? '#C4813D' : 'rgba(196,129,61,0.6)'),
                        fill: isArea,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => 'Rp ' + Number(c.raw).toLocaleString('id-ID') } } },
                    scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000) + 'k' } } },
                    animation: { duration: 600 },
                }
            });
            // Top
            const ctxT = document.getElementById('chart-top').getContext('2d');
            if (charts.top) charts.top.destroy();
            charts.top = new Chart(ctxT, {
                type: 'bar',
                data: {
                    labels: this.topMenus.map(m=>m.menu_name),
                    datasets: [{ data: this.topMenus.map(m=>m.qty), backgroundColor: '#5D3F26' }],
                },
                options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, animation:{duration:600} }
            });
            // Category
            const ctxC = document.getElementById('chart-cat').getContext('2d');
            if (charts.cat) charts.cat.destroy();
            charts.cat = new Chart(ctxC, {
                type: 'doughnut',
                data: {
                    labels: this.byCategory.map(c=>c.name),
                    datasets: [{ data: this.byCategory.map(c=>c.total), backgroundColor: ['#5D3F26','#C4813D','#A07B4D','#7A5635','#D4944E','#4A2F1B'] }],
                },
                options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{position:'right'}, tooltip:{ callbacks:{ label: c=> c.label + ': Rp ' + Number(c.raw).toLocaleString('id-ID') } } } }
            });
        },

        setChartType(t) { this.chartType = t; this.renderCharts(); },

        getHeatCount(h, dow) {
            const e = this.heatmap.find(x => x.h===h && x.dow===dow);
            return e ? e.cnt : 0;
        },
        getHeatLevel(h, dow) {
            const c = this.getHeatCount(h, dow);
            const max = Math.max(1, ...this.heatmap.map(x=>x.cnt));
            const r = c / max;
            if (c===0) return 0;
            if (r<0.25) return 1;
            if (r<0.5) return 2;
            if (r<0.75) return 3;
            return 4;
        },
        rupiah(v) { return 'Rp ' + Math.round(v).toLocaleString('id-ID'); },
        exportUrl(format) { return `{{ route('owner.reports.export') }}?format=${format}&from=${this.from}&to=${this.to}`; }
    }
}
</script>
@endsection
