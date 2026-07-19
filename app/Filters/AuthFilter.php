<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Si l'utilisateur n'est pas connecté en session, on le renvoie au login
        // if (! session()->has('user')) {
        //     return redirect()->to('/')->with('error', 'Veuillez vous connecter d\'abord.');
        // }

        if (!session()->has('user')) {
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire ici
    }
}