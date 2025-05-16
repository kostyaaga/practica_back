<?php

namespace Middlewares;

use Src\Auth\Auth;

class AdminMiddleware
{
    public function handle()
    {
        if (Auth::user()->role !== 'Администратор') {
            http_response_code(403);
            exit('403 Forbidden — доступ только для администраторов');
        }
        return true;
    }
}
