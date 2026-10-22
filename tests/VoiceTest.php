<?php

use Illuminate\Foundation\Testing\TestCase;
use Twilio\Http\Client as HttpClient;
use Twilio\Http\Response;
use Twilio\Rest\Client;
use Twilio\Security\RequestValidator;

final class VoiceTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.twilio.sid' => 'AC'.str_repeat('a', 32), 'services.twilio.token' => 'test-token', 'services.twilio.from' => '+15005550006', 'services.twilio.sound_url' => 'https://media.example.invalid/sounds']);
    }

    public function testOutboundTwimlRemainsPlayableWithValidSignature(): void
    {
        $path = '/outbound/verification/code';
        $signature = (new RequestValidator('test-token'))->computeSignature('http://localhost'.$path, []);
        $response = $this->withHeader('X-Twilio-Signature', $signature)->post($path);
        $response->assertOk()->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertSame('https://media.example.invalid/sounds/verification/code.mp3', (string) $xml->Play);
    }

    public function testInvalidWebhookSignatureIsRejected(): void
    {
        $this->post('/outbound/verification/code')->assertForbidden();
    }

    public function testCallUsesMaintainedSdkAndRetainsResponse(): void
    {
        $transport = new RecordingTwilioTransport();
        $this->app->instance(Client::class, new Client(config('services.twilio.sid'), 'test-token', null, null, $transport));
        $this->postJson('/call', ['phoneNumber' => '+15005550009', 'carpeta' => 'verification', 'sonido' => 'code'])
            ->assertOk()->assertExactJson(['message' => 'Call incoming!', 'carpeta' => 'verification', 'sonido' => 'code']);
        $this->assertSame('POST', $transport->requests[0]['method']);
        $this->assertSame('+15005550009', $transport->requests[0]['data']['To']);
        $this->assertSame('+15005550006', $transport->requests[0]['data']['From']);
        $this->assertSame('http://localhost/outbound/verification/code', $transport->requests[0]['data']['Url']);
    }

    public function testBrowserCallRequiresCsrfToken(): void
    {
        $this->app['env'] = 'local';
        $this->postJson('/call', ['phoneNumber' => '+15005550009', 'carpeta' => 'verification', 'sonido' => 'code'])->assertStatus(419);
    }

    public function testInvalidCallInputNeverReachesTwilio(): void
    {
        $this->postJson('/call', ['phoneNumber' => 'invalid', 'carpeta' => '../escape', 'sonido' => 'code'])->assertUnprocessable();
    }

    public function testProviderFailureReturnsSafeError(): void
    {
        $transport = new RecordingTwilioTransport(500);
        $this->app->instance(Client::class, new Client(config('services.twilio.sid'), 'test-token', null, null, $transport));
        $this->postJson('/call', ['phoneNumber' => '+15005550009', 'carpeta' => 'verification', 'sonido' => 'code'])
            ->assertStatus(502)->assertExactJson(['message' => 'The call provider could not accept the call.']);
    }
}

final class RecordingTwilioTransport implements HttpClient
{
    public array $requests = [];

    public function __construct(private int $status = 201) {}

    public function request(string $method, string $url, array $params = [], array $data = [], array $headers = [], ?string $user = null, ?string $password = null, ?int $timeout = null, ?Twilio\AuthStrategy\AuthStrategy $authStrategy = null): Response
    {
        $this->requests[] = compact('method', 'url', 'data');
        return new Response($this->status, json_encode(['sid' => 'CA'.str_repeat('b', 32), 'status' => 'queued', 'code' => 20001, 'message' => 'provider internal error']), []);
    }
}
