<?php

namespace App\Controllers\Web;

class DriverController extends BaseWebController
{
    // -------------------------------------------------------------------------
    // CHAUFFEUR — Planning du jour
    // Route : GET /driver/planning
    // -------------------------------------------------------------------------
    public function planning()
    {
        $user = $this->requireAuthApi();

        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $userRole = $this->userRole();
        $layout = match($userRole) {
            'super_admin' => 'web/layouts/super_admin',
            'admin'       => 'web/layouts/admin',
            'driver'      => 'web/layouts/driver',
            default       => 'web/layouts/driver',
        };

        $data = [
            'layout'     => $layout,
            'title'      => 'Kashala Trans — Mon Planning',
            'pageTitle'  => 'Mon Planning',
            'user'       => $user,
            'programmes' => [],
        ];

        return view('web/driver/planning', $data);
    }
}
