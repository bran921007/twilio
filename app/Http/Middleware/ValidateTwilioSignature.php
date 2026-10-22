<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

final class ValidateTwilioSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('services.twilio.token');
        abort_unless(is_string($token) && $token !== '' && (new RequestValidator($token))->validate(
            $request->header('X-Twilio-Signature', ''), $request->fullUrl(), $request->post()
        ), 403);

        return $next($request);
    }
}
