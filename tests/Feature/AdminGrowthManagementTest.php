<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGrowthManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_growth_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        User::factory()->create();
        ContactInquiry::create([
            'name' => 'Sender',
            'email' => 'sender@example.com',
            'subject' => '問い合わせ',
            'message' => "確認したい内容です。\n2行目も確認します。",
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.growth.index'));

        $response
            ->assertOk()
            ->assertSee('成長管理', false)
            ->assertSee('問い合わせ履歴', false)
            ->assertDontSee('解約理由', false)
            ->assertSee('whitespace-pre-line', false)
            ->assertSee('2行目も確認します。', false);
    }

    public function test_regular_user_cannot_view_growth_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this
            ->actingAs($user)
            ->get(route('admin.growth.index'))
            ->assertForbidden();
    }

    public function test_admin_can_mark_contact_inquiry_as_handled(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $inquiry = ContactInquiry::create([
            'name' => 'Sender',
            'email' => 'sender@example.com',
            'subject' => '問い合わせ',
            'message' => '確認したい内容です。',
        ]);

        $this
            ->actingAs($admin)
            ->patch(route('admin.growth.inquiries.handle', $inquiry))
            ->assertRedirect(route('admin.growth.index'));

        $this->assertDatabaseHas('contact_inquiries', [
            'id' => $inquiry->id,
            'status' => ContactInquiry::STATUS_HANDLED,
            'handled_by' => $admin->id,
        ]);
    }

    public function test_cancel_feedback_route_no_longer_records_reason(): void
    {
        $user = User::factory()->create([
            'subscription_plan' => User::SUBSCRIPTION_ACTIVE,
            'subscription_status' => 'active',
            'stripe_customer_id' => 'cus_test',
        ]);

        $this
            ->actingAs($user)
            ->post(route('subscriptions.cancel-feedback'))
            ->assertRedirect(route('subscriptions.index'));

        $this->assertDatabaseCount('subscription_cancellation_feedback', 0);
    }
}
