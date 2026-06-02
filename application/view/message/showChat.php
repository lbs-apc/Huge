<div class="container">
    <h1>Chat with <?= $this->user->user_name; ?></h1>
    
    <div class="box">
        <?php $this->renderFeedbackMessages(); ?>

        <a href="<?php echo Config::get('URL'); ?>message/index">Back</a>
        <br><br>

        <div id="chat-window" style="height:300px; border:1px solid black; overflow:scroll;">
        <?php if (!empty($this->conversation)) { ?>
            <?php foreach ($this->conversation as $m) { ?>
                <div>
                    <b><?= ($m->sender_id == Session::get('user_id') ? 'You' : $this->user->user_name); ?>:</b>
                    <?= htmlspecialchars($m->content); ?>
                    <br>
                    <small><?= $m->created_at; ?></small>
                </div>
                <hr>
            <?php } ?>
        <?php } else { ?>
            <p>No messages yet.</p>
        <?php } ?>
        </div>

        <form method="post" action="<?php echo Config::get('URL'); ?>message/send/<?php echo $this->user->user_id; ?>">
            <input type="text" name="text" />
            <input type="submit" value="Send" />
        </form>

    </div>
</div>
