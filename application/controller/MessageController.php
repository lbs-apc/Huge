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

        $this->View->render('message/index', array(
            'users' => $filtered_users
        ));
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
