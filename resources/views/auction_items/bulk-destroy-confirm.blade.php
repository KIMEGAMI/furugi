<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-red-300">商品全削除の確認</h2>
                <p class="mt-1 text-sm font-bold text-cyan-100">登録済みの商品だけを全削除します。ユーザー、カテゴリ、契約情報は削除されません。</p>
            </div>

            <a href="{{ route('auction-items.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-200 px-5 py-3 text-sm font-bold text-slate-700 shadow transition hover:bg-slate-300">
                商品一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-5">
                    <ul class="space-y-1 text-sm font-bold text-black">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-3xl border border-red-200 bg-white p-6 shadow-xl">
                <p class="text-xs font-black tracking-widest text-red-700">DANGER ZONE</p>
                <h3 class="mt-2 text-2xl font-black text-slate-950">商品 {{ number_format($itemCount) }} 件を削除します</h3>
                <p class="mt-3 text-sm font-bold leading-7 text-slate-700">
                    この操作は商品データと商品画像を削除します。削除後、画面から元に戻すことはできません。削除対象は現在ログイン中のアカウントの商品だけです。
                </p>

                <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <h4 class="text-base font-black text-amber-950">削除前にCSVバックアップを取得してください</h4>
                    <p class="mt-2 text-sm font-bold leading-7 text-amber-800">
                        復元しやすい形で残す場合は、CSV管理から「復元用CSV」を出力してください。商品画像ファイル自体はCSVに含まれないため、画像も必要な場合は別途控えてください。
                    </p>
                    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                        @if ($hasActiveSubscription)
                            <a href="{{ route('sales.restore-csv') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-5 py-3 text-sm font-black text-white shadow hover:bg-amber-600">
                                復元用CSVを出力
                            </a>
                            <a href="{{ route('sales.backup-csv') }}" class="inline-flex items-center justify-center rounded-xl border border-amber-300 bg-white px-5 py-3 text-sm font-black text-amber-900 shadow hover:bg-amber-100">
                                全商品バックアップCSV
                            </a>
                        @else
                            <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center justify-center rounded-xl bg-cyan-500 px-5 py-3 text-sm font-black text-slate-950 shadow hover:bg-cyan-400">
                                PremiumでCSVバックアップを使う
                            </a>
                        @endif
                    </div>
                </div>

                <form action="{{ route('auction-items.bulk-destroy') }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    @method('DELETE')

                    <label class="flex gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-black text-red-950">
                        <input type="checkbox" name="confirm_delete_all_items" value="1" class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500">
                        <span>商品だけを全削除すること、削除後に画面から戻せないことを確認しました。</span>
                    </label>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <a href="{{ route('auction-items.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-200 px-6 py-3 text-sm font-black text-black shadow hover:bg-slate-300">
                            キャンセル
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-700 px-6 py-3 text-sm font-black text-white shadow hover:bg-red-800">
                            商品を全削除する
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
