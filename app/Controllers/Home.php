<?php

namespace App\Controllers;
use App\Models\LoginModel;

class Home extends BaseController
{
    public function __construct()
    {
        $this->model = new LoginModel();
    }

    public function index()
    {
        return view('welcome_message');
    }

    public function login()
    {
        return view ('login');
    }

    public function login_post()
    {
        $un = $this->request->getPost('username');
        $user = $this->model->where('username', $un)->first();
        if($user)
        {
            if(password_verify($this->request->getPost('password'), $user['password'] ))
            {
                $session = \Config\Services::session();
                $sessiondata = [
                    'login' => true,
                    'username' => $user['username'],
                    'role' => $user['role']
                ];
                $session->set($sessiondata);
                if ($this->isadmin()){ return redirect()->to(site_url('admin')); }
                else{ return redirect()->to(site_url('secret')); }
            }
            else
            {
                return "login failed";
            }
        }
        else
        {
            return "login failed";
        }
    }

    public function register()
    {
        return view ('register');
    }

    public function register_post()
    {
        $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $hash,
            'role' => 'member'
        ];
        $this->model->insert($data);
        return redirect()->to(site_url('login'));
    }

    public function secret()
    {
        if (!$this->isloggedin()) { return redirect()->to(site_url('login'));}
        $session = \Config\Services::session();
        return view ('secretpage');
    }

    public function adminsecret()
    {
        if (!$this->isadmin()) { return redirect()->to(site_url('login'));}
        $session = \Config\Services::session();
        $data['users'] = $this->model->findAll();
        return view ('adminpage', $data);
    }

    public function logout()
    {
        $session = \Config\Services::session();
        $session->destroy();
        return redirect()->to(site_url('login'));
    }

    public function delete()
    {
        if (!$this->isadmin()) { return redirect()->to(site_url('login'));}
        $id = $this->request->uri->getSegment(2);
        $this->model->where('id', $id)->delete();
        return redirect()->to(site_url('admin'));
    }

    public function makeadmin()
    {
        if (!$this->isadmin()) { return redirect()->to(site_url('login'));}
        $id = $this->request->uri->getSegment(2);
        $this->model->set('role', 'admin')->where('id', $id)->update();
        return redirect()->to(site_url('admin'));
    }
}