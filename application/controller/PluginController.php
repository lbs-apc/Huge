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
        $plugins = PluginModel::getAllPlugins();
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

        PluginModel::installPlugin($id);
        Redirect::to('plugin/index');
    }

    public function toggle($id)
    {
        if (!is_numeric($id)) {
            Redirect::to('plugin/index');
        }

        PluginModel::togglePlugin($id);
        Redirect::to('plugin/index');
    }

}
