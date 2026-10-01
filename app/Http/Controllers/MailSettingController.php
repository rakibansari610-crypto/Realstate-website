<?php

namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// Symfony Mailer (bypasses .env config)
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MailSettingController extends Controller
{
    public function edit()
    {
        $orgId = Auth::user()->org_id;
        $setting = MailSetting::firstOrNew(['org_id' => $orgId]);
        return view('org.settings.mail', compact('setting'));
    }

    public function update(Request $request)
    {
        $orgId = Auth::user()->org_id;

        $data = $request->validate([
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|integer|min:1',
            'mail_username'     => 'nullable|string',
            'mail_password'     => 'nullable|string', // leave blank to keep
            'mail_encryption'   => ['nullable', Rule::in(['ssl', 'tls'])], // null/none not set = plain/STARTTLS via query
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'nullable|string|max:190',
        ]);

        $setting = MailSetting::firstOrNew(['org_id' => $orgId]);
        $setting->fill([
            'mail_host'         => $data['mail_host'],
            'mail_port'         => $data['mail_port'],
            'mail_username'     => $data['mail_username'] ?? null,
            'mail_encryption'   => $data['mail_encryption'] ?? null,
            'mail_from_address' => $data['mail_from_address'],
            'mail_from_name'    => $data['mail_from_name'] ?: config('app.name'),
        ]);

        // Only update password if provided
        if (!empty($data['mail_password'])) {
            $setting->mail_password = $data['mail_password']; // encrypted cast
        }

        $setting->save();

        return back()->with('success', 'SMTP settings saved.');
    }
    private function decryptIfNeeded(?string $value): string
    {
        $value = (string) ($value ?? '');
        if ($value === '') return '';

        // Try to decrypt; if it fails, assume it's already plain text
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return $value; // treat as plain
        }
    }
    public function test(Request $request)
    {
        $request->validate([
            'test_to' => 'required|email',
        ]);

        $orgId = Auth::user()->org_id;

        /** @var MailSetting $cfg */
        $cfg = MailSetting::where('org_id', $orgId)->firstOrFail();

        // Pull values from DB
        $host       = (string) $cfg->mail_host;
        $port       = (int)    $cfg->mail_port;
        $username   = (string) ($cfg->mail_username ?? '');
        $password = $this->decryptIfNeeded($cfg->mail_password);
        
        $encryption = $cfg->mail_encryption; // 'ssl' | 'tls' | null
        $fromAddr   = (string) $cfg->mail_from_address;
        $fromName   = (string) ($cfg->mail_from_name ?: config('app.name'));

        // Build DSN from DB config
        $scheme  = ($encryption === 'ssl') ? 'smtps' : 'smtp';
        $useAuth = filled($username) && filled($password);   // only if BOTH present
        $auth    = $useAuth ? rawurlencode($username) . ':' . rawurlencode($password) . '@' : '';

        $query   = ($encryption === 'tls') ? '?encryption=tls' : '';
        $query  .= ($query ? '&' : '?') . 'timeout=30';

        $dsn = "{$scheme}://{$auth}{$host}:{$port}{$query}";

        try {
            $transport = Transport::fromDsn($dsn);
            $mailer    = new SymfonyMailer($transport);

            $email = (new Email())
                ->from(new Address($fromAddr, $fromName))
                ->to($request->input('test_to'))
                ->subject('SMTP Test • ' . $fromName)
                ->text("This is a test email sent using ONLY the saved SMTP settings.\nHost: {$host}\nPort: {$port}\nEnc: " . ($encryption ?: 'none'));

            $mailer->send($email);

            return response()->json([
                'ok'      => true,
                'message' => 'Test email sent to ' . $request->input('test_to'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
