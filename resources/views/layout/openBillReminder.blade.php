@php
    $reminderActiveBills = \App\Models\Cart::openBills()
        ->where('store_id', auth()->user()->store->id ?? 0)
        ->with('cartMenus')
        ->get();
    $reminderTotal = $reminderActiveBills->sum('total_amount');
    $reminderShow = now()->day >= 26 && $reminderActiveBills->isNotEmpty();
@endphp

@if ($reminderShow)
    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 flex items-start gap-3 shadow-sm">
        <i class="fas fa-bell text-amber-600 text-xl mt-0.5"></i>
        <div class="flex-1">
            <p class="text-sm text-amber-900 font-bold">
                Recap Reminder: {{ $reminderActiveBills->count() }} open bill{{ $reminderActiveBills->count() > 1 ? 's' : '' }} still active.
            </p>
            <p class="text-xs text-amber-800 mt-1">
                Outstanding total: <span class="font-mono font-bold">Rp {{ number_format($reminderTotal, 0, ',', '.') }}</span>.
                Settle or cancel pending bills before monthly recap.
            </p>
            <ul class="mt-2 space-y-0.5 text-xs text-amber-800">
                @foreach ($reminderActiveBills as $rb)
                    <li>
                        <span class="font-semibold">{{ $rb->customer_name ?? $rb->chair->name ?? 'Unnamed' }}</span>
                        — Rp {{ number_format($rb->total_amount, 0, ',', '.') }}
                        <span class="text-amber-600">({{ $rb->opened_at?->format('d M H:i') ?? '-' }})</span>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('order') }}" class="inline-block mt-2 text-xs font-bold text-amber-700 hover:text-amber-900 underline">
                Go to Orders →
            </a>
        </div>
    </div>
@endif
