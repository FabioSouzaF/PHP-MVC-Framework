<?php

namespace Core\Http;

interface Middleware
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @return bool True if request should proceed, False to abort.
     */
    public function handle(Request $request): bool;
}
