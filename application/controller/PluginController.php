<?php

class PluginController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index()
    {
        $this->View->render('plugin/index');
    }

    public function upload()
    {
        Redirect::to('gallery/index');
    }

    public function delete($id)
    {
        Redirect::to('gallery/index');
    }

    public function toggle($id)
    {
        Redirect::to('gallery/index');
    }

}
