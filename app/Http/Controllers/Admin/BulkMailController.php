<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBulkMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class BulkMailController extends Controller
{
    private const UNSAFE_MAILERS = ['log', 'array'];

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.bulk-mail.index', [
            'recipientCount' => $this->recipientQuery()->count(),
            'fromAddress' => config('admin_mail.from_address'),
            'fromName' => config('admin_mail.from_name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:'.config('admin_mail.max_subject_length')],
            'body' => ['required', 'string', 'max:'.config('admin_mail.max_body_length')],
        ]);

        if (! $this->canSendMail()) {
            return redirect()
                ->route('admin.bulk-mail.index')
                ->withInput()
                ->with('error', 'メール送信設定が未完了です。MAIL_MAILER、MAIL_FROM_ADDRESS、ADMIN_MAIL_FROM_ADDRESSを確認してください。');
        }

        $sentCount = 0;
        $failedCount = 0;
        $chunkSize = max(1, (int) config('admin_mail.chunk_size'));

        $this->recipientQuery()->chunkById($chunkSize, function ($users) use ($validated, &$sentCount, &$failedCount): void {
            foreach ($users as $recipient) {
                try {
                    Mail::to($recipient->email, $recipient->name)
                        ->send((new AdminBulkMail($validated['subject'], $validated['body']))
                            ->from(config('admin_mail.from_address'), config('admin_mail.from_name')));

                    $sentCount++;
                } catch (Throwable) {
                    $failedCount++;
                }
            }
        });

        if ($failedCount > 0) {
            return redirect()
                ->route('admin.bulk-mail.index')
                ->with('error', "一斉メール送信が一部失敗しました。送信成功 {$sentCount} 件 / 失敗 {$failedCount} 件");
        }

        return redirect()
            ->route('admin.bulk-mail.index')
            ->with('status', "一斉メールを送信しました。送信件数 {$sentCount} 件");
    }

    private function authorizeAdmin(Request $request): User
    {
        $user = $request->user();

        abort_unless($user?->isAdmin(), 403);

        return $user;
    }

    private function recipientQuery()
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select(['id', 'name', 'email']);
    }

    private function canSendMail(): bool
    {
        $mailer = config('mail.default');
        $fromAddress = config('admin_mail.from_address');

        return is_string($mailer)
            && ! in_array($mailer, self::UNSAFE_MAILERS, true)
            && is_string($fromAddress)
            && filter_var($fromAddress, FILTER_VALIDATE_EMAIL) !== false;
    }
}
