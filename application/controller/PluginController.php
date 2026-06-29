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
        $user_id = Session::get('user_id');
        $plugins = PluginModel::getAllPlugins($user_id);
        $this->View->render('plugin/index', array('plugins' => $plugins));
    }

    public function upload()
    {
        Redirect::to('plugin/index');
    }

    public function delete($id)
    {
        Redirect::to('plugin/index');
    }

    public function install($id)
    {
        if (!is_numeric($id)) {
            Redirect::to('plugin/index');
        }

        $user_id = Session::get('user_id');
        PluginModel::installPlugin($user_id, $id);
        Redirect::to('plugin/index');
    }

    public function toggle($id)
    {
        if (!is_numeric($id)) {
            Redirect::to('plugin/index');
        }

        $user_id = Session::get('user_id');
        PluginModel::togglePlugin($user_id, $id);
        Redirect::to('plugin/index');
    }

    public function uninstall($id)
    {
        if (!is_numeric($id)) {
            Redirect::to('plugin/index');
        }

        $user_id = Session::get('user_id');
        PluginModel::uninstallPlugin($user_id, $id);
        Redirect::to('plugin/index');
    }

}
