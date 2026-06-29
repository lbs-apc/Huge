<div class="container">
    <h1>Plugin Store</h1>

    <?php $this->renderFeedbackMessages(); ?>

    <div class="box">
        <h3>Plugins</h3>

        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Name</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Version</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Status</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($this->plugins)) { foreach ($this->plugins as $plugin) { ?>
                    <tr>
                        <td style="padding:8px; border-bottom:1px solid #eee;"><?php echo $this->encodeHTML($plugin->plugin_name); ?></td>
                        <td style="padding:8px; border-bottom:1px solid #eee;"><?php echo $this->encodeHTML($plugin->version); ?></td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">
                            <?php echo ($plugin->user_active == 1 ? 'Active' : 'Inactive'); ?>
                            <br/>
                            <small>Downloads: <?php echo (int)$plugin->total_installed; ?></small>
                        </td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">
                            <?php if ((int)$plugin->user_installed <= 0) { ?>
                                <form action="<?php echo Config::get('URL') . 'plugin/install/' . $plugin->plugin_id; ?>" method="POST" class="plugin-action-form">
                                    <button type="submit" class="btn-plugin btn-install">Install</button>
                                </form>
                            <?php } else { ?>
                                <form action="<?php echo Config::get('URL') . 'plugin/toggle/' . $plugin->plugin_id; ?>" method="POST" class="plugin-action-form">
                                    <?php if ($plugin->user_active == 1) { ?>
                                        <button type="submit" class="btn-plugin btn-deactivate">Deactivate</button>
                                    <?php } else { ?>
                                        <button type="submit" class="btn-plugin btn-activate">Activate</button>
                                    <?php } ?>
                                </form>
                                <form action="<?php echo Config::get('URL') . 'plugin/uninstall/' . $plugin->plugin_id; ?>" method="POST" class="plugin-action-form">
                                    <button type="submit" class="btn-plugin btn-uninstall">Uninstall</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } } else { ?>
                    <tr><td colspan="4" style="padding:8px;">No plugins found.</td></tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>
