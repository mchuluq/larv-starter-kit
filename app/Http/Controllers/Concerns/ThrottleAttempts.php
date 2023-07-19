<?php 

namespace App\Http\Controllers\Concerns;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ThrottleAttempts {

    protected function hasTooManyAttempts(Request $req):bool {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($req), $this->maxAttempts()
        );
    }

    protected function incrementAttempts(Request $request): void{
        $this->limiter()->hit($this->throttleKey($request), $this->decayMinutes() * 60);
    }

    protected function sendLockoutResponse(Request $req): void{
        $seconds = $this->limiter()->availableIn($this->throttleKey($req));
        throw ValidationException::withMessages([
            $this->throttleKeyName() => [Lang::get('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }

    protected function clearAttempts(Request $request): void{
        $this->limiter()->clear($this->throttleKey($request));
    }

    protected function fireLockoutEvent(Request $request): void{
        event(new Lockout($request));
    }

    protected function throttleKey(Request $request): string{
        return Str::lower($request->input($this->throttleKeyName())).'|'.$request->ip();
    }

    protected function limiter(): RateLimiter{
        return app(RateLimiter::class);
    }

    public function maxAttempts(): int{
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }

    public function decayMinutes(): int{
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }

    public function throttleKeyName(): string{
        return property_exists($this, 'throttleKeyName') ? $this->throttleKeyName : 'username';
    }
}