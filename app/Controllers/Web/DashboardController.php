<?php

namespace App\Controllers\Web;

class DashboardController extends BaseWebController
{
    public function index()
    {
        // vue
        return view('web/super_admin/dashboard');
    }
}