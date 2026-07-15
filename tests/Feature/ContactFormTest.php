<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    public function test_contact_form_can_be_rendered(): void
    {
        $response = $this->get('/contact');

        $response->assertOk();
        $response->assertSee('お問い合わせフォーム', false);
        $response->assertSee('name="message"', false);
    }

    public function test_contact_form_rejects_ng_words(): void
    {
        $response = $this->from('/contact')->post('/contact', [
            'name' => 'Test User',
            'email' => 'sender@example.com',
            'subject' => '確認',
            'message' => '死ね',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHasErrors('message');
    }

    public function test_contact_form_accepts_clean_message_without_logging_mail(): void
    {
        Mail::fake();

        $response = $this->from('/contact')->post('/contact', [
            'name' => 'Test User',
            'email' => 'sender@example.com',
            'subject' => '使い方について',
            'message' => '商品登録の使い方を確認したいです。',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        Mail::assertNothingSent();
    }
}
