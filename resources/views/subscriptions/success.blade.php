<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-3xl bg-white p-8 text-center shadow-xl">
                <p class="text-sm font-black text-emerald-700">CHECKOUT COMPLETED</p>
                <h1 class="mt-3 text-3xl font-black text-slate-900">お申し込みありがとうございます</h1>
                <p class="mt-4 text-sm font-bold leading-7 text-slate-600">
                    Stripeからの通知を受け取り次第、Premium機能が有効になります。数十秒たっても切り替わらない場合は、画面を更新してください。
                </p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('dashboard') }}" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800">
                        ダッシュボードへ
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="rounded-xl bg-slate-200 px-5 py-3 text-sm font-black text-slate-700 hover:bg-slate-300">
                        Premium状態を確認
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
