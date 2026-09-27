<?php

namespace Tests\Feature;

use App\Domain\Settings\Models\LegalDocument;
use App\Domain\Settings\Models\Setting;
use App\Filament\Pages\Settings\DisclaimerSettingsPage;
use App\Filament\Pages\Settings\EmailSettingsPage;
use App\Filament\Pages\Settings\GeneralSettingsPage;
use App\Filament\Pages\Settings\LegalSettingsPage;
use App\Filament\Pages\Settings\PaymentSettingsPage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_general_settings_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/general');
        $response->assertSuccessful();
        $response->assertSee('Pengaturan Umum');
        $response->assertSee('Identitas &amp; Informasi Brand Platform', false);
    }

    public function test_admin_can_access_payment_settings_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $response = $this->actingAs($admin)->get('/admin/payment');
        $response->assertSuccessful();
        $response->assertSee('Payment');
        $response->assertSee('Midtrans');
        $response->assertSee('Xendit');
    }

    public function test_admin_can_access_email_settings_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $response = $this->actingAs($admin)->get('/admin/email');
        $response->assertSuccessful();
        $response->assertSee('Email');
        $response->assertSee('SMTP');
    }

    public function test_admin_can_access_legal_settings_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $response = $this->actingAs($admin)->get('/admin/legal');
        $response->assertSuccessful();
        $response->assertSee('Legal');
        $response->assertSee('Terms of Service');
        $response->assertSee('Privacy Policy');
    }

    public function test_admin_can_access_disclaimer_settings_page(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $response = $this->actingAs($admin)->get('/admin/disclaimer');
        $response->assertSuccessful();
        $response->assertSee('Disclaimer');
        $response->assertSee('Peringatan Risiko');
    }

    public function test_admin_can_save_general_settings(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $this->actingAs($admin);

        Livewire::test(GeneralSettingsPage::class)
            ->fillForm([
                'site_name' => 'TradingEdu Pro Updated',
                'site_tagline' => 'Akademi Trading Finansial Terbaik',
                'support_email' => 'support@tradingedu.test',
                'support_whatsapp' => '+628999888777',
                'default_timezone' => 'Asia/Makassar',
                'default_currency' => 'IDR',
                'default_locale' => 'id',
                'maintenance_mode' => false,
                'allow_user_registration' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('TradingEdu Pro Updated', Setting::get('site_name', group: 'general'));
        $this->assertEquals('support@tradingedu.test', Setting::get('support_email', group: 'general'));
    }

    public function test_admin_can_save_payment_settings(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $this->actingAs($admin);

        Livewire::test(PaymentSettingsPage::class)
            ->fillForm([
                'midtrans_enabled' => true,
                'midtrans_environment' => 'production',
                'midtrans_merchant_id' => 'MIDTRANS_LIVE_123',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1370001234567',
                'bank_account_holder' => 'PT TRADING EDU GLOBAL',
                'order_expiry_hours' => 48,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Bank Mandiri', Setting::get('bank_name', group: 'payment'));
        $this->assertEquals('1370001234567', Setting::get('bank_account_number', group: 'payment'));
    }

    public function test_admin_can_save_email_settings_and_test(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $this->actingAs($admin);

        Livewire::test(EmailSettingsPage::class)
            ->fillForm([
                'mail_mailer' => 'smtp',
                'mail_host' => 'smtp.sendgrid.net',
                'mail_port' => '587',
                'mail_encryption' => 'tls',
                'mail_from_name' => 'TradingEdu Alerts',
                'mail_from_address' => 'alerts@tradingedu.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->set('test_email_recipient', 'test@example.com')
            ->call('sendTestEmail');

        $this->assertEquals('smtp.sendgrid.net', Setting::get('mail_host', group: 'email'));
        $this->assertEquals('TradingEdu Alerts', Setting::get('mail_from_name', group: 'email'));
    }

    public function test_admin_can_save_legal_settings_and_sync_documents(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $this->actingAs($admin);

        Livewire::test(LegalSettingsPage::class)
            ->fillForm([
                'terms_title' => 'Updated Syarat & Ketentuan v2.0',
                'terms_version' => 'v2.0',
                'terms_content' => 'Ketentuan baru untuk platform trading edutech.',
                'privacy_title' => 'Updated Kebijakan Privasi v2.0',
                'privacy_version' => 'v2.0',
                'privacy_content' => 'Kebijakan privasi diperketat sesuai UU PDP.',
                'refund_title' => 'Updated Kebijakan Refund v2.0',
                'refund_version' => 'v2.0',
                'refund_content' => 'Ketentuan refund 14 hari penuh.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('v2.0', Setting::get('terms_version', group: 'legal'));

        $terms = LegalDocument::getCurrent('terms');
        $this->assertNotNull($terms);
        $this->assertEquals('v2.0', $terms->version);
        $this->assertEquals('Updated Syarat & Ketentuan v2.0', $terms->title);
    }

    public function test_admin_can_save_disclaimer_settings_and_sync_documents(): void
    {
        $admin = User::where('email', 'admin@tradingedu.com')->first();

        $this->actingAs($admin);

        Livewire::test(DisclaimerSettingsPage::class)
            ->fillForm([
                'disclaimer_title' => 'Penafian Risiko Pasar Derivatif',
                'disclaimer_version' => 'v2.0',
                'disclaimer_body' => 'Trading mengandung risiko tinggi hingga kehilangan seluruh modal.',
                'not_financial_advice_text' => 'Bukan ajakan investasi atau anjuran finansial.',
                'past_performance_warning' => 'Hasil masa lalu tidak menjamin hasil masa depan.',
                'show_modal_on_first_visit' => true,
                'require_checkbox_on_checkout' => true,
                'show_footer_disclaimer' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('v2.0', Setting::get('disclaimer_version', group: 'disclaimer'));

        $disclaimer = LegalDocument::getCurrent('disclaimer');
        $this->assertNotNull($disclaimer);
        $this->assertEquals('Penafian Risiko Pasar Derivatif', $disclaimer->title);
    }
}
