<?php

namespace SisRoute\Middleware;

use SisRoute\Http\Request;

interface SisRouteMiddleware
{
    public function handler(Request $request);
}