@extends('layouts.customer')
@section('title', 'Menu — Meja ' . $table->table_number)
@section('content')
<div x-data="customerCart()" class="min-h-screen pb-32">
    <!-- Header -->
    <div class="bg-coffee-900 text-cream-50 px-5 pt-8 pb-12 rounded-b-3xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-widest text-cream-100/60">CaféPos</div>
                <div class="font-display text-2xl mt-1">Meja No. {{ $table->table_number }}</div>
                <div class="text-sm text-cream-100/70 mt-0.5">Kapasitas {{ $table->capacity }} orang · Selamat datang ☕</div>
            </div>
            <button onclick="toggleDarkMode()" class="p-2 rounded-full bg-coffee-700 hover:bg-coffee-600 transition" data-testid="dark-toggle">
                <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10l1.4 1.4M3 12h2m14 0h2M5.6 18.4l1.4-1.4m10-10l1.4-1.4M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                <svg class="w-5 h-5 dark:hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
        </div>
    </div>

    <div class="px-4 -mt-6">
        <input type="search" x-model="search" placeholder="🔍 Cari menu..." class="input shadow-lg" data-testid="search-input">
    </div>

    <!-- Category tabs -->
    <div class="px-4 mt-4 overflow-x-auto">
        <div class="flex gap-2 pb-2 min-w-max">
            <button @click="cat=null" :class="cat===null ? 'bg-caramel-500 text-white' : 'bg-white dark:bg-coffee-800 text-coffee-700 dark:text-cream-100'"
                class="px-4 py-2 rounded-full text-sm font-semibold transition shadow-sm" data-testid="cat-all">Semua</button>
            @foreach($categories as $c)
            <button @click="cat={{ $c->id }}" :class="cat==={{ $c->id }} ? 'bg-caramel-500 text-white' : 'bg-white dark:bg-coffee-800 text-coffee-700 dark:text-cream-100'"
                class="px-4 py-2 rounded-full text-sm font-semibold transition shadow-sm whitespace-nowrap" data-testid="cat-{{ $c->id }}">{{ $c->icon }} {{ $c->name }}</button>
            @endforeach
        </div>
    </div>

    <!-- Menu grid -->
    <div class="px-4 mt-5 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        @foreach($menus as $m)
        @php
            $imgs = $m->images->pluck('path')->all();
            $primary = $imgs[0] ?? null;
            $habis = !$m->is_available || $m->stock_qty <= 0;
        @endphp
        <div class="card overflow-hidden transition transform hover:-translate-y-0.5 hover:shadow-lg"
             x-show="(cat===null || cat==={{ $m->category_id }}) && '{{ strtolower($m->name) }}'.includes(search.toLowerCase())"
             data-testid="menu-card-{{ $m->id }}">
            <div class="relative aspect-square bg-coffee-100 dark:bg-coffee-700 {{ $habis ? 'opacity-50 grayscale' : '' }}">
                @if($primary)
                    <img src="{{ asset('storage/' . $primary) }}" alt="{{ $m->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-5xl">{{ $m->category->icon ?? '🍽' }}</div>
                @endif
                @if($habis)
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="bg-red-500 text-white text-xs px-3 py-1.5 rounded-full font-bold">🚫 HABIS</span>
                    </div>
                @endif
                @if(!$habis && is_array($m->labels))
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @foreach($m->labels as $lab)
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider
                                @if($lab==='baru') bg-blue-500 text-white
                                @elseif($lab==='favorit') bg-amber-400 text-coffee-900
                                @elseif($lab==='pedas') bg-red-500 text-white
                                @endif">
                                {{ $lab }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="p-3">
                <div class="font-display font-semibold text-coffee-900 dark:text-cream-50 text-sm line-clamp-1">{{ $m->name }}</div>
                <div class="text-[11px] text-coffee-500 dark:text-cream-100/60 line-clamp-2 mt-0.5 h-8">{{ $m->description }}</div>
                <div class="flex items-center justify-between mt-2">
                    <div class="font-mono-num font-bold text-caramel-600">Rp {{ number_format((float)$m->price, 0, ',', '.') }}</div>
                    <button @click="add({{ $m->id }}, '{{ addslashes($m->name) }}', {{ $m->price }})" {{ $habis ? 'disabled' : '' }}
                        class="w-8 h-8 rounded-full bg-caramel-500 text-white text-lg font-bold hover:bg-caramel-600 active:scale-90 disabled:bg-gray-300 transition shadow-md"
                        data-testid="add-btn-{{ $m->id }}">+</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Sticky cart bar -->
    <div x-show="items.length>0" x-transition.translate.y-full
         class="fixed bottom-4 inset-x-4 z-30 bg-coffee-900 text-cream-50 rounded-2xl shadow-2xl p-4 flex items-center justify-between cursor-pointer hover:bg-coffee-800 transition"
         @click="showCart=true" data-testid="cart-bar">
        <div>
            <div class="text-xs text-cream-100/60">🛒 <span x-text="totalQty"></span> item</div>
            <div class="font-mono-num font-bold text-lg" x-text="rupiah(total)"></div>
        </div>
        <div class="bg-caramel-500 px-4 py-2 rounded-xl font-semibold">Pesan ›</div>
    </div>

    <!-- Cart modal -->
    <div x-show="showCart" x-transition.opacity class="fixed inset-0 z-40 bg-black/60 flex items-end md:items-center justify-center" @click.self="showCart=false">
        <div class="bg-white dark:bg-coffee-800 w-full md:max-w-lg rounded-t-3xl md:rounded-3xl max-h-[90vh] flex flex-col" x-transition.translate.y-full>
            <div class="p-5 border-b border-coffee-100 dark:border-coffee-700 flex items-center justify-between">
                <div class="font-display text-xl font-semibold">Keranjang</div>
                <button @click="showCart=false" class="text-2xl">✕</button>
            </div>
            <div class="flex-1 overflow-y-auto p-5 space-y-3">
                <template x-for="(it, idx) in items" :key="idx">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-cream-50 dark:bg-coffee-700/50">
                        <div class="flex-1">
                            <div class="font-semibold text-sm" x-text="it.name"></div>
                            <div class="text-xs text-coffee-500" x-text="rupiah(it.price) + ' × ' + it.qty"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="dec(idx)" class="w-7 h-7 rounded-full bg-coffee-200 dark:bg-coffee-600">−</button>
                            <span class="w-6 text-center font-bold" x-text="it.qty"></span>
                            <button @click="inc(idx)" class="w-7 h-7 rounded-full bg-caramel-500 text-white">+</button>
                        </div>
                    </div>
                </template>
                <div class="pt-3">
                    <label class="label">Catatan untuk dapur/kasir</label>
                    <textarea x-model="notes" rows="2" class="input resize-none" placeholder="Contoh: gula sedikit..."></textarea>
                </div>
                <div class="pt-2 border-t border-coffee-100 dark:border-coffee-700">
                    <div class="font-semibold mb-2">Pilih Metode Pembayaran</div>
                    <div class="grid grid-cols-3 gap-2">
                        @if($settings->cash_active)
                        <button type="button" @click="method='cash'" :class="method==='cash' ? 'border-caramel-500 bg-caramel-50 dark:bg-caramel-500/20' : 'border-coffee-200 dark:border-coffee-600'"
                            class="border-2 rounded-xl p-3 text-center transition" data-testid="pay-cash">
                            <div class="text-2xl">💵</div><div class="text-xs font-semibold mt-1">Cash</div>
                        </button>
                        @endif
                        @if($settings->transfer_active)
                        <button type="button" @click="method='transfer'" :class="method==='transfer' ? 'border-caramel-500 bg-caramel-50 dark:bg-caramel-500/20' : 'border-coffee-200 dark:border-coffee-600'"
                            class="border-2 rounded-xl p-3 text-center transition" data-testid="pay-transfer">
                            <div class="text-2xl">🏦</div><div class="text-xs font-semibold mt-1">Transfer</div>
                        </button>
                        @endif
                        @if($settings->qris_active)
                        <button type="button" @click="method='qris'" :class="method==='qris' ? 'border-caramel-500 bg-caramel-50 dark:bg-caramel-500/20' : 'border-coffee-200 dark:border-coffee-600'"
                            class="border-2 rounded-xl p-3 text-center transition" data-testid="pay-qris">
                            <div class="text-2xl">📱</div><div class="text-xs font-semibold mt-1">QRIS</div>
                        </button>
                        @endif
                    </div>
                </div>
                <!-- Bank list -->
                <div x-show="method==='transfer'" class="space-y-2 pt-2">
                    @foreach($banks as $b)
                    <div class="p-3 rounded-xl bg-coffee-50 dark:bg-coffee-700/50">
                        <div class="text-xs text-coffee-500 uppercase tracking-wider">{{ $b->bank_name }}</div>
                        <div class="font-mono-num font-bold text-lg" id="rek-{{ $b->id }}">{{ $b->account_number }}</div>
                        <div class="text-sm">a.n. {{ $b->account_name }}</div>
                    </div>
                    @endforeach
                </div>
                <div x-show="method==='qris'" class="text-center py-2">
                    <img src="{{ url('/payment/qris') }}" onerror="this.style.display='none'" class="mx-auto rounded-xl max-w-xs" alt="QRIS">
                    <div class="text-xs text-coffee-500 mt-2">Scan dengan e-wallet / m-banking</div>
                </div>
            </div>
            <div class="p-5 border-t border-coffee-100 dark:border-coffee-700">
                <div class="flex justify-between mb-3">
                    <span class="text-coffee-500">Total</span>
                    <span class="font-mono-num font-bold text-2xl text-caramel-600" x-text="rupiah(total)"></span>
                </div>
                <form method="POST" action="{{ route('customer.order', $table->qr_token) }}">
                    @csrf
                    <input type="hidden" name="payment_method" :value="method">
                    <input type="hidden" name="notes" :value="notes">
                    <template x-for="(it,idx) in items" :key="idx">
                        <div>
                            <input type="hidden" :name="'items['+idx+'][menu_item_id]'" :value="it.id">
                            <input type="hidden" :name="'items['+idx+'][quantity]'" :value="it.qty">
                        </div>
                    </template>
                    <button type="submit" :disabled="!method || items.length===0" class="btn-primary w-full" data-testid="submit-order">Kirim Pesanan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function customerCart() {
    return {
        search: '',
        cat: null,
        items: JSON.parse(localStorage.getItem('cart_{{ $table->qr_token }}') || '[]'),
        notes: '',
        method: '',
        showCart: false,
        get totalQty() { return this.items.reduce((s,i)=>s+i.qty,0); },
        get total() { return this.items.reduce((s,i)=>s+i.qty*i.price,0); },
        save() { localStorage.setItem('cart_{{ $table->qr_token }}', JSON.stringify(this.items)); },
        add(id, name, price) {
            const e = this.items.find(i=>i.id===id);
            if (e) e.qty++; else this.items.push({id,name,price:Number(price),qty:1});
            this.save();
            window.toast && window.toast(name + ' ditambahkan ke keranjang', 'success');
        },
        inc(i) { this.items[i].qty++; this.save(); },
        dec(i) { this.items[i].qty--; if (this.items[i].qty<=0) this.items.splice(i,1); this.save(); },
        rupiah(v) { return 'Rp ' + Math.round(v).toLocaleString('id-ID'); },
    }
}
</script>
@endsection
