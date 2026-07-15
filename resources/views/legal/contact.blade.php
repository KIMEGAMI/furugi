<x-legal-layout
    title="お問い合わせ"
    eyebrow="CONTACT"
    description="FURUGIへのお問い合わせ方法、返信目安、サポート対象についての案内です。"
>
    <h2>お問い合わせフォーム</h2>
    <p>FURUGIの利用方法、不具合、Premiumプラン、アカウント、個人情報の取り扱いに関するお問い合わせを受け付けています。</p>
    <p>できるだけ状況がわかるように、発生した画面、操作内容、表示されたエラーの概要を添えてください。</p>

    @if (session('success'))
        <div class="not-prose mt-8 rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-bold text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('legal.contact.store') }}" class="not-prose mt-8 space-y-6 rounded-lg border border-slate-200 bg-slate-50 p-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-black text-slate-700">お名前</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                maxlength="{{ config('contact.max_name_length') }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            @error('name')
                <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-black text-slate-700">メールアドレス</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                maxlength="255"
                autocomplete="email"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            @error('email')
                <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="subject" class="block text-sm font-black text-slate-700">件名</label>
            <input
                id="subject"
                name="subject"
                type="text"
                value="{{ old('subject') }}"
                required
                maxlength="{{ config('contact.max_subject_length') }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            @error('subject')
                <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="message" class="block text-sm font-black text-slate-700">お問い合わせ内容</label>
            <textarea
                id="message"
                name="message"
                rows="7"
                required
                maxlength="{{ config('contact.max_message_length') }}"
                class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >{{ old('message') }}</textarea>
            @error('message')
                <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-md border border-amber-200 bg-amber-50 p-4 text-xs font-semibold leading-6 text-amber-900">
            罵倒、脅迫、差別的な表現、性的嫌がらせ、スパムを含む内容は送信できません。パスワード、APIキー、決済カード番号などの機密情報は入力しないでください。
        </div>

        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-5 py-3 text-sm font-black text-white shadow-sm hover:bg-blue-800">
            送信する
        </button>
    </form>

    <h2>返信目安</h2>
    <p>内容を確認し、必要に応じて順次返信します。</p>
    <p>障害対応や詳細な調査が必要な場合は、返信まで時間がかかることがあります。</p>

    <h2>注意事項</h2>
    <p>不適切な表現を検出した場合、検出した単語そのものは表示せず、送信を停止します。</p>
    <p>安全のため、パスワードや決済情報などの機密情報は送らないでください。</p>
</x-legal-layout>
