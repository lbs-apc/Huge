<div class="container">
    <h1>My Messages</h1>
    <div class="box">

        <?php $this->renderFeedbackMessages(); ?>

        <h3>Groups</h3>
        <a href="<?= Config::get('URL'); ?>message/createGroup"><b>+ Create New Group</b></a>
        <br><br>
        <table border="1">
            <thead>
            <tr>
                <th>Id</th>
                <th>Group Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($this->groups)) { ?>
                <?php foreach ($this->groups as $group) { ?>
                    <tr>
                        <td><?= $group->id; ?></td>
                        <td><?= $group->name; ?></td>
                        <td>
                            <a href="<?= Config::get('URL') . 'message/showGroupChat/' . $group->id; ?>">Chat</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="3">No groups yet.</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <br>
        <h3>Start a conversation</h3>

        <table border="1">            <thead>
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
