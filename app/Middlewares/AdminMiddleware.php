<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;

class AdminMiddleware
{
    public function handle(Request $request)
    {
        if (Auth::user()->role !== 'Администратор') {
            http_response_code(403);
            exit('403 Forbidden — доступ только для администраторов');
        }
        return $request;
    }
}
