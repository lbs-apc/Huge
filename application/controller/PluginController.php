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
        if (PluginModel::installPlugin($user_id, $id)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_PLUGIN_INSTALLED_SUCCESSFUL'));
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_PLUGIN_INSTALLED_FAILED'));
        }
        Redirect::to('plugin/index');
    }

    public function toggle($id)
    {
        if (!is_numeric($id)) {
            Redirect::to('plugin/index');
        }

        $user_id = Session::get('user_id');
        if (PluginModel::togglePlugin($user_id, $id)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_PLUGIN_TOGGLED_SUCCESSFUL'));
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_PLUGIN_TOGGLED_FAILED'));
        }
        Redirect::to('plugin/index');
    }

    public function uninstall($id)
    {
        if (!is_numeric($id)) {
            Redirect::to('plugin/index');
        }

        $user_id = Session::get('user_id');
        if (PluginModel::uninstallPlugin($user_id, $id)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_PLUGIN_UNINSTALLED_SUCCESSFUL'));
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_PLUGIN_UNINSTALLED_FAILED'));
        }
        Redirect::to('plugin/index');
    }

}
