<div class="container">
    <h1>My Messages</h1>
    <div class="box">

        <?php $this->renderFeedbackMessages(); ?>

        <h3>Start a conversation</h3>

        <table border="1">
            <thead>
            <tr>
                <th>Id</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->users as $user) { ?>
                <tr>
                    <td><?= $user->user_id; ?></td>
                    <td><?= $user->user_name; ?></td>
                    <td>
                        <form action="<?= Config::get('URL') . 'message/showChat/' . $user->user_id; ?>">
                            <input type="submit" value="Open Chat" />
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
