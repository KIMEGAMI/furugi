<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-blue-400">CSV登録</h2>
                <p class="mt-1 text-sm text-cyan-200">
                    FURUGI形式CSVと、ヤフオク売上CSVの変換登録を行います。
                </p>
            </div>

            <a href="{{ route('auction-items.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-200 px-5 py-3 text-sm font-bold text-slate-700 shadow transition hover:bg-slate-300">
                商品一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @foreach (['success' => 'blue', 'error' => 'red'] as $flashKey => $color)
                @if(session($flashKey))
                    <div class="mb-6 rounded-2xl border border-{{ $color }}-200 bg-{{ $color }}-50 px-6 py-5">
                        <p class="font-bold text-{{ $color }}-700">{{ session($flashKey) }}</p>
                    </div>
                @endif
            @endforeach

            @if($errors->has('csv_file'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-5">
                    <p class="font-bold text-black">{{ $errors->first('csv_file') }}</p>
                </div>
            @endif

            @if($errors->has('yahoo_csv_file'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-5">
                    <p class="font-bold text-black">{{ $errors->first('yahoo_csv_file') }}</p>
                </div>
            @endif

            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-xs font-black tracking-widest text-slate-500">FURUGI CSV</p>
                            <h3 class="mt-2 text-xl font-black text-slate-950">FURUGI形式CSVを一括登録</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">
                                FURUGI MANAGER用に整えたCSVをそのまま取り込みます。1行目はヘッダー行として扱います。
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('csvImportHelp').showModal()" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-black text-blue-700 hover:bg-blue-100">
                            CSV項目を確認
                        </button>
                    </div>

                    <form action="{{ route('auction-items.import') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
                        @csrf
                        <div class="lg:col-span-8">
                            <label for="csv_file" class="block text-sm font-black text-slate-700">CSVファイル</label>
                            <input id="csv_file" type="file" name="csv_file" accept=".csv,.txt" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                最低限、management_id と title の列が必要です。
                            </p>
                        </div>
                        <div class="lg:col-span-4">
                            <button type="submit" class="w-full rounded-xl bg-slate-800 px-5 py-3 text-sm font-bold text-white shadow transition hover:bg-slate-900">
                                CSVを取り込む
                            </button>
                        </div>
                    </form>
                </section>

                <section class="rounded-3xl border border-cyan-200 bg-white p-6 shadow">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-xs font-black tracking-widest text-cyan-700">YAHOO AUCTIONS</p>
                            <h3 class="mt-2 text-xl font-black text-slate-950">ヤフオク売上CSVを変換して一括登録</h3>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">
                                ヤフオクの売上CSVをFURUGI形式に変換し、SOLD商品として登録します。外部CSV変換は現在ヤフオクのみ対応しています。
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('yahooCsvImportHelp').showModal()" class="rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm font-black text-cyan-700 hover:bg-cyan-100">
                            ヤフオクCSV項目を確認
                        </button>
                    </div>

                    <form action="{{ route('auction-items.import.yahoo-auctions') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
                        @csrf
                        <div class="lg:col-span-8">
                            <label for="yahoo_csv_file" class="block text-sm font-black text-slate-700">ヤフオク売上CSVファイル</label>
                            <input id="yahoo_csv_file" type="file" name="yahoo_csv_file" accept=".csv,.txt" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                状態が「売上確定」で、商品IDが入っている行だけを取り込みます。
                            </p>
                        </div>
                        <div class="lg:col-span-4">
                            <button type="submit" class="w-full rounded-xl bg-cyan-700 px-5 py-3 text-sm font-bold text-white shadow transition hover:bg-cyan-800">
                                変換して一括登録
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <dialog id="csvImportHelp" class="w-11/12 max-w-4xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/70">
                <div class="max-h-[85vh] overflow-y-auto bg-white p-6 text-slate-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-950">FURUGI形式CSVの項目</h3>
                            <p class="mt-2 text-sm font-bold leading-6 text-slate-600">
                                CSVの1行目はヘッダー行として扱います。必須は management_id と title です。外部サービスのCSVを直接取り込む形式ではありません。
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('csvImportHelp').close()" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-200">閉じる</button>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full border-collapse text-left text-sm">
                            <thead class="bg-slate-100 text-xs font-black text-slate-700">
                                <tr>
                                    <th class="border border-slate-200 px-3 py-2">CSVヘッダー名</th>
                                    <th class="border border-slate-200 px-3 py-2">画面項目</th>
                                    <th class="border border-slate-200 px-3 py-2">説明</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">management_id</td><td class="border border-slate-200 px-3 py-2">管理ID</td><td class="border border-slate-200 px-3 py-2">必須。同じユーザー内では重複登録できません。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">title</td><td class="border border-slate-200 px-3 py-2">商品タイトル</td><td class="border border-slate-200 px-3 py-2">必須。商品一覧や詳細画面に表示されます。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">comment</td><td class="border border-slate-200 px-3 py-2">コメント</td><td class="border border-slate-200 px-3 py-2">任意。商品の説明やメモとして保存します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">platform</td><td class="border border-slate-200 px-3 py-2">出品先</td><td class="border border-slate-200 px-3 py-2">任意の出品先名です。外部CSV変換はヤフオクのみ対応しています。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">大ジャンル</td><td class="border border-slate-200 px-3 py-2">大ジャンル</td><td class="border border-slate-200 px-3 py-2">登録済みジャンル名と一致した場合、小ジャンルと組み合わせて反映します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">小ジャンル</td><td class="border border-slate-200 px-3 py-2">小ジャンル</td><td class="border border-slate-200 px-3 py-2">大ジャンル配下の小ジャンル名です。一致しない場合は未設定になります。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">purchase_price</td><td class="border border-slate-200 px-3 py-2">仕入れ値</td><td class="border border-slate-200 px-3 py-2">半角数字で入力します。空欄は0円扱いです。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">sold_price</td><td class="border border-slate-200 px-3 py-2">売値</td><td class="border border-slate-200 px-3 py-2">販売予定額または販売額です。半角数字で入力します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">shipping_fee</td><td class="border border-slate-200 px-3 py-2">送料</td><td class="border border-slate-200 px-3 py-2">半角数字で入力します。利益計算に使用します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">sales_fee_rate</td><td class="border border-slate-200 px-3 py-2">販売手数料率</td><td class="border border-slate-200 px-3 py-2">10 や 8.8 など、%を除いた数値で入力します。空欄なら出品先ごとの標準率を使用します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">status</td><td class="border border-slate-200 px-3 py-2">ステータス</td><td class="border border-slate-200 px-3 py-2">selling、sold、draft のいずれかです。空欄や不明な値は selling になります。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-mono font-bold">sold_at</td><td class="border border-slate-200 px-3 py-2">SOLD日</td><td class="border border-slate-200 px-3 py-2">status が sold の場合に使用します。例: 2026-07-19</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5 rounded-xl bg-slate-50 p-4 text-sm font-bold leading-6 text-slate-700">
                        ヘッダー例: management_id,title,comment,platform,大ジャンル,小ジャンル,purchase_price,sold_price,shipping_fee,sales_fee_rate,status,sold_at
                    </div>
                </div>
            </dialog>

            <dialog id="yahooCsvImportHelp" class="w-11/12 max-w-4xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/70">
                <div class="max-h-[85vh] overflow-y-auto bg-white p-6 text-slate-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-950">ヤフオク売上CSVの変換内容</h3>
                            <p class="mt-2 text-sm font-bold leading-6 text-slate-600">
                                1行目はヘッダー行として扱います。状態が「売上確定」で、商品IDが入っている行だけを取り込みます。チャージ行や商品IDが「-」の行はスキップします。
                            </p>
                        </div>
                        <button type="button" onclick="document.getElementById('yahooCsvImportHelp').close()" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-200">閉じる</button>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full border-collapse text-left text-sm">
                            <thead class="bg-slate-100 text-xs font-black text-slate-700">
                                <tr>
                                    <th class="border border-slate-200 px-3 py-2">ヤフオクCSV列</th>
                                    <th class="border border-slate-200 px-3 py-2">FURUGI項目</th>
                                    <th class="border border-slate-200 px-3 py-2">変換内容</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">商品ID</td><td class="border border-slate-200 px-3 py-2 font-mono">management_id</td><td class="border border-slate-200 px-3 py-2">管理IDとして登録します。同じ商品IDが既にある場合はスキップします。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">取引内容</td><td class="border border-slate-200 px-3 py-2 font-mono">title</td><td class="border border-slate-200 px-3 py-2">商品タイトルとして登録します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">取引日</td><td class="border border-slate-200 px-3 py-2 font-mono">sold_at</td><td class="border border-slate-200 px-3 py-2">SOLD日として登録します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">決済金額</td><td class="border border-slate-200 px-3 py-2 font-mono">sold_price</td><td class="border border-slate-200 px-3 py-2">購入者が支払った金額を売値として登録します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">落札システム利用料 / 販売手数料</td><td class="border border-slate-200 px-3 py-2 font-mono">sales_fee</td><td class="border border-slate-200 px-3 py-2">どちらかに入っている手数料を合計して登録します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">送料</td><td class="border border-slate-200 px-3 py-2 font-mono">shipping_fee</td><td class="border border-slate-200 px-3 py-2">送料として登録します。</td></tr>
                                <tr><td class="border border-slate-200 px-3 py-2 font-bold">状態</td><td class="border border-slate-200 px-3 py-2 font-mono">status</td><td class="border border-slate-200 px-3 py-2">「売上確定」の行だけをSOLDとして登録します。</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </dialog>
        </div>
    </div>
</x-app-layout>
