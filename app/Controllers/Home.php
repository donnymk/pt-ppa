<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\FollowupModel;
use App\Models\LoginModel;

class Home extends BaseController {

    public function initController(
            RequestInterface $request,
            ResponseInterface $response,
            LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        // Add your code here.
    }

    public function index() {
        // initialize the session
        $session = \Config\Services::session();
        // default value
        $data['username'] = null;
        // cek session login
        if ($session->has('username')) {
            $data['username'] = $session->username;
            $data['role'] = $session->role;
        }        

        // QUERY MELALUI MODEL
        $model = new FollowupModel();
        $data['countFollowUp'] = $model->countFollowUp();

        return view('dasbor', $data);
    }

    public function login() {
        return view('login');
    }

    // proses login
    public function loginproses() {
        // terima data dari form login
        $inputUsername = $this->request->getPost('inputUsername');
        $inputPassword = $this->request->getPost('inputPassword');

        // initialize the session
        $session = \Config\Services::session();

        // default password db = null karena belum dilakukan query database
        $password_db = null;

        // QUERY MELALUI MODEL
        $model = new LoginModel();
        $select_admin = $model->authAdmin($inputUsername);

        // jika data ditemukan
        foreach ($select_admin as $value):
            $role = $value->role;
            $password_db = $value->password;
        endforeach;

        // jika password benar
        if (password_verify($inputPassword, $password_db)) {
            $newdata = [
                'username' => $inputUsername,
                'role' => $role,
                'logged_in' => true,
            ];
            $session->set($newdata);

            session_write_close();

            // Go to specific URI
            return redirect()->to(base_url('followup-cbm/'));
        } else {
            echo 'Invalid password.';
        }
    }

    public function logout() {
        // initialize the session
        $session = \Config\Services::session();
        $session->destroy();

        // Go to specific URI
        return redirect()->to(base_url('followup-cbm/'));
    }

}
