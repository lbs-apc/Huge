<div class="container">
    <h1>Plugin Store</h1>

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
                            <?php echo ($plugin->active == 1 ? 'Active' : 'Inactive'); ?>
                            <br/>
                            <small>Installs: <?php echo (int)$plugin->installed; ?></small>
                        </td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">
                            <?php if ((int)$plugin->installed <= 0) { ?>
                                <a href="<?php echo Config::get('URL'); ?>plugin/install/<?php echo $plugin->plugin_id; ?>" class="btn">Install</a>
                            <?php } else { ?>
                                <a href="<?php echo Config::get('URL'); ?>plugin/toggle/<?php echo $plugin->plugin_id; ?>" class="btn">
                                    <?php echo ($plugin->active == 1 ? 'Deactivate' : 'Activate'); ?>
                                </a>
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
