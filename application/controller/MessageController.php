<?php
class MessageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    public function index()
    {
        $all_users = UserModel::getPublicProfilesOfAllUsers();
        $filtered_users = array();

        foreach ($all_users as $user) {
            if ($user->user_id != Session::get('user_id')) {
                $filtered_users[] = $user;
            }
        }

        // Get groups
        $groups = MessageModel::getGroupsForUser(Session::get('user_id'));

        $this->View->render('message/index', array(
            'users' => $filtered_users,
            'groups' => $groups
        ));
    }

    public function createGroup()
    {
        $this->View->render('message/createGroup', array(
            'users' => UserModel::getPublicProfilesOfAllUsers()
        ));
    }

    public function createGroup_action()
    {
        $group_name = Request::post('group_name');
        $user_ids = Request::post('users'); // array of user ids

        if ($group_name) {
            $group_id = MessageModel::createGroup($group_name, $user_ids);
            Session::add('feedback_positive', "Group '" . $group_name . "' created!");
            Redirect::to('message/showGroupChat/' . $group_id);
        } else {
            Session::add('feedback_negative', "Please provide a group name.");
            Redirect::to('message/createGroup');
        }
    }

    public function showGroupChat($group_id)
    {
        if (isset($group_id)) {
            $group = MessageModel::getGroup($group_id);
            $conversation = MessageModel::getGroupMessages($group_id);

            $this->View->render('message/showChat', array(
                'group' => $group,
                'conversation' => $conversation
            ));
        } else {
            Redirect::home();
        }
    }

    public function sendGroupMessage($group_id)
    {
        $message_text = Request::post('text');

        if ($group_id && $message_text) {
            MessageModel::sendGroupMessage($group_id, $message_text);
            Session::add('feedback_positive', "Sent!");
        }

        Redirect::to('message/showGroupChat/' . $group_id);
    }

    public function showChat($user_id)
    {
        if (isset($user_id)) {
            MessageModel::markMessagesAsReadFrom($user_id, Session::get('user_id'));

            $user_data = UserModel::getPublicProfileOfUser($user_id);
            $chat_data = MessageModel::getConversationWith($user_id);

            $this->View->render('message/showChat', array(
                'user' => $user_data,
                'conversation' => $chat_data
            ));
        } else {
            Redirect::home();
        }
    }

    public function send($receiver_id)
    {
        $message_text = Request::post('text');

        if (!$message_text) {
            $message_text = Request::get('text');
        }

        if ($receiver_id && $message_text) {
            MessageModel::sendMessage($receiver_id, $message_text);
            Session::add('feedback_positive', "Sent!");
        }

        Redirect::to('message/showChat/' . $receiver_id);
    }
}
