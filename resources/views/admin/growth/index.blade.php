<x-app-layout>
    @php
        $reasonLabels = \App\Models\SubscriptionCancellationFeedback::REASONS;
    @endphp

    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-black text-blue-700">ADMIN</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-950">成長管理</h1>
                    <p class="mt-2 text-sm font-bold text-slate-700">
                        契約者300人を目指すための登録状況、問い合わせ、解約理由を確認します。
                    </p>
                </div>

                <a href="{{ route('admin.users.index') }}" class="inline-flex w-fit items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-black text-white hover:bg-slate-800">
                    ユーザー一覧
                </a>
            </div>

            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow">
                    <p class="text-xs font-black tracking-widest text-slate-500">登録ユーザー</p>
                    <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($summary['total_users']) }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-600">30日内更新 {{ number_format($summary['active_users_30_days']) }}件</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow">
                    <p class="text-xs font-black tracking-widest text-cyan-700">Premium</p>
                    <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($summary['premium_users']) }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-600">転換率 {{ number_format($summary['premium_rate'], 1) }}%</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow">
                    <p class="text-xs font-black tracking-widest text-emerald-700">商品データ</p>
                    <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($summary['total_items']) }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-600">SOLD {{ number_format($summary['sold_items']) }}件</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow">
                    <p class="text-xs font-black tracking-widest text-amber-700">対応待ち</p>
                    <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($summary['open_inquiries']) }}</p>
                    <p class="mt-2 text-sm font-bold text-slate-600">解約理由 {{ number_format($summary['cancellation_feedback_count']) }}件</p>
                </div>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-2">
                <article class="rounded-2xl bg-white p-5 shadow">
                    <h2 class="text-xl font-black text-slate-950">問い合わせ履歴</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($recentInquiries as $inquiry)
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-sm font-black text-slate-950">{{ $inquiry->subject }}</p>
                                        <p class="mt-1 text-xs font-bold text-slate-600">{{ $inquiry->name }} / {{ $inquiry->email }}</p>
                                    </div>
                                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-black {{ $inquiry->status === \App\Models\ContactInquiry::STATUS_HANDLED ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $inquiry->status === \App\Models\ContactInquiry::STATUS_HANDLED ? '対応済み' : '未対応' }}
                                    </span>
                                </div>
                                <p class="mt-3 line-clamp-3 text-sm font-semibold leading-6 text-slate-700">{{ $inquiry->message }}</p>
                                <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <time class="text-xs font-bold text-slate-500">{{ $inquiry->created_at?->format('Y/m/d H:i') }}</time>
                                    @if ($inquiry->status !== \App\Models\ContactInquiry::STATUS_HANDLED)
                                        <form method="POST" action="{{ route('admin.growth.inquiries.handle', $inquiry) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-black text-white hover:bg-slate-800">
                                                対応済みにする
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-4 text-sm font-bold text-slate-600">問い合わせはまだありません。</p>
                        @endforelse
                    </div>
                </article>

                <article class="rounded-2xl bg-white p-5 shadow">
                    <h2 class="text-xl font-black text-slate-950">解約理由</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($cancellationReasons as $row)
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                                <span class="text-sm font-black text-slate-800">{{ $reasonLabels[$row->reason] ?? $row->reason }}</span>
                                <span class="text-lg font-black text-slate-950">{{ number_format($row->total) }}</span>
                            </div>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-4 text-sm font-bold text-slate-600">解約理由はまだ記録されていません。</p>
                        @endforelse
                    </div>

                    <h3 class="mt-6 text-lg font-black text-slate-950">直近の声</h3>
                    <div class="mt-3 space-y-3">
                        @forelse ($recentCancellationFeedback as $feedback)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-sm font-black text-slate-950">{{ $feedback->user?->email ?? '削除済みユーザー' }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-600">{{ $reasonLabels[$feedback->reason] ?? $feedback->reason }} / {{ $feedback->created_at?->format('Y/m/d H:i') }}</p>
                                @if ($feedback->detail)
                                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-700">{{ $feedback->detail }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-4 text-sm font-bold text-slate-600">直近の解約理由はありません。</p>
                        @endforelse
                    </div>
                </article>
            </section>

            <section class="mt-6 rounded-2xl bg-white p-5 shadow">
                <h2 class="text-xl font-black text-slate-950">新規登録ユーザー</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-black text-slate-600">
                            <tr>
                                <th class="px-4 py-3">ユーザー</th>
                                <th class="px-4 py-3">契約状態</th>
                                <th class="px-4 py-3">登録日</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentUsers as $listedUser)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-black text-slate-950">{{ $listedUser->name }}</div>
                                        <div class="text-xs font-bold text-slate-600">{{ $listedUser->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-700">{{ $listedUser->subscription_status ?: 'free' }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-700">{{ $listedUser->created_at?->format('Y/m/d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
