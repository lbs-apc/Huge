<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#users-table').DataTable();
    });
</script>

<div class="container">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" />

    <h1>MessageController/index</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here ?</h3>
        <div>
            This controller/action/view shows a list of all your chats with users.
            <br>
            <br>
        </div>
        <div>
            <table id="users-table" class="overview-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Avatar</td>
                    <td>Username</td>
                    <td>Message</td>
                </tr>
                <br>
                </thead>
                <!--<?php foreach ($this->users as $user) { ?>
                    <tr class="<?= ($user->user_active == 0 ? 'inactive' : 'active'); ?>">
                        <td><?= $user->user_id; ?></td>
                        <td class="avatar">
                            <?php if (isset($user->user_avatar_link)) { ?>
                                <img src="<?= $user->user_avatar_link; ?>" />
                            <?php } ?>-->
                        </td>
                        <td><?= $user->user_name; ?></td>
                        <td><?= $user->user_email; ?></td>
                        <td><?= ($user->user_active == 0 ? 'No' : 'Yes'); ?></td>
                        <td><?= $user->user_account_type_name; ?></td>
                        <td>
                            <a href="<?= Config::get('URL') . 'message/showChat/' . $user->user_id; ?>">Profile</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
