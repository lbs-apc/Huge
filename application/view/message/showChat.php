<div class="container">
    <?php if (isset($this->group)) { ?>
        <h1>Group: <?= $this->group->name; ?></h1>
    <?php } else { ?>
        <h1>Chat with <?= $this->user->user_name; ?></h1>
    <?php } ?>
    
    <div class="box">
        <?php $this->renderFeedbackMessages(); ?>

        <a href="<?php echo Config::get('URL'); ?>message/index">Back</a>

        <section class="discussion">
        <?php if (!empty($this->conversation)) { ?>
            <?php 
            $msg_count = count($this->conversation);
            for ($i = 0; $i < $msg_count; $i++) {
                
                $current = $this->conversation[$i];
                
                $prev = ($i > 0) ? $this->conversation[$i-1] : null;
                $next = ($i < $msg_count - 1) ? $this->conversation[$i+1] : null;

                $is_me = ($current->sender_id == Session::get('user_id'));
                $type = $is_me ? 'recipient' : 'sender';
                
                $is_same_as_prev = ($prev && $prev->sender_id == $current->sender_id);
                $is_same_as_next = ($next && $next->sender_id == $current->sender_id);
                
                $extra_class = "";
                if (!$is_same_as_prev && $is_same_as_next) {
                    $extra_class = "first";
                } else if ($is_same_as_prev && $is_same_as_next) {
                    $extra_class = "middle";
                } else if ($is_same_as_prev && !$is_same_as_next) {
                    $extra_class = "last";
                }
            ?>
                <div class="bubble <?= $type; ?> <?= $extra_class; ?>">
                    <?php if (isset($this->group) && !$is_me && !$is_same_as_prev) { ?>
                        <small><b><?= $current->sender_name; ?></b></small><br>
                    <?php } ?>
                    <?= htmlspecialchars($current->content); ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No messages yet.</p>
        <?php } ?>
        </section>

        <?php if (isset($this->group)) { ?>
            <form method="post" action="<?php echo Config::get('URL'); ?>message/sendGroupMessage/<?php echo $this->group->id; ?>">
        <?php } else { ?>
            <form method="post" action="<?php echo Config::get('URL'); ?>message/send/<?php echo $this->user->user_id; ?>">
        <?php } ?>
            <input type="text" name="text" placeholder="Type a message..." />
            <input type="submit" value="Send" />
        </form>

    </div>
</div>
