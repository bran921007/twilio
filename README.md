# Twilio voice example

Requires PHP 8.2+ and maintained Laravel 12, Twilio PHP SDK 8 and Stripe PHP SDK 22. Run `composer install`, copy `.env.example` to `.env`, configure Twilio credentials, caller number and public HTTPS application/sound URLs, then `php artisan key:generate`. Serve only `public/` and install production dependencies with `composer install --no-dev`.

Composer uses `vendor-runtime/`; artisan, HTTP and tests all use that autoloader. The tracked `vendor/` is an inactive historical snapshot and must never be loaded or served.

`POST /call` accepts the existing `phoneNumber`, `carpeta` and `sonido` fields and preserves the success JSON. Browser requests use the Laravel session CSRF token. Phone and sound-path fields are validated before reaching Twilio. `POST /outbound/{carpeta}/{sonido}` still generates Play TwiML and now validates the provider's `X-Twilio-Signature`; only that callback is exempt from browser CSRF. Configure the application's public URL and trusted proxy behavior correctly for signature verification. Provider acceptance is not proof the recipient received a call.

Run `composer test` for HTTP validation, genuine SDK request serialization using an in-memory transport, signed and unsigned webhook callbacks, TwiML and safe provider failure. Tests need no credentials or network. No real calls, merge, deployment, VPS change or production database operation is included. Plan a fresh application key/session cutover from Laravel 4.
