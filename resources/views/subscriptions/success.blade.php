<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-6 text-center shadow-xl md:p-8">
                <p class="text-sm font-black tracking-[0.18em] text-cyan-700">STRIPE CHECKOUT</p>
                <h1 class="mt-3 text-3xl font-black text-slate-900">契約登録を受け付けました</h1>
                <p class="mt-4 text-sm font-bold leading-7 text-slate-600">
                    Stripeからの通知を受信すると契約状態が自動更新されます。
                    反映されない場合は、少し時間をおいて契約状態を確認してください。
                </p>
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('subscriptions.index') }}" class="rounded-lg bg-cyan-700 px-6 py-3 text-sm font-black text-white shadow hover:bg-cyan-800">
                        契約状態を確認する
                    </a>
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-800 shadow hover:bg-slate-50">
                        HOMEへ戻る
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
