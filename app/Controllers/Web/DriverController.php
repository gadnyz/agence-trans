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
        $user = session()->get('user');

        $data = [
            'title'     => 'Kashala Trans — Mon Planning',
            'pageTitle' => 'Mon Planning',
            'user'      => $user,
            // À remplacer par une requête réelle : $programmesModel->getForDriver($user['id'])
            'programmes' => [],
        ];

        return view('web/driver/planning', $data);
    }
}
