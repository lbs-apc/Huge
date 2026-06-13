<div class="container">
    <h1>My Image Gallery</h1>

    <div class="box">
        <h3>Upload new image</h3>
        <form action="<?php echo Config::get('URL'); ?>gallery/upload" method="post" enctype="multipart/form-data">
            <input type="file" name="image" required />
            <button type="submit">Upload</button>
        </form>
    </div>

    <div class="box">
        <ul class="image-zoom-grid">
            <?php foreach ($this->images as $image) { ?>
                <li>
                    <figure>
                        <div class="image-frame">
                            <img src="<?php echo Config::get('URL'); ?>gallery/show/<?php echo $image->image_id; ?>" style="width:100%">
                        </div>
                        <figcaption>
                            <?php if ($image->owner_id == Session::get('user_id')) { ?>
                                <a href="<?php echo Config::get('URL'); ?>gallery/toggle/<?php echo $image->image_id; ?>">
                                    <?php if ($image->is_shared == 1) { echo "Make Private"; } else { echo "Share"; } ?>
                                </a> |
                                <a href="<?php echo Config::get('URL'); ?>gallery/delete/<?php echo $image->image_id; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php } else { ?>
                                <span>Public Image</span>
                            <?php } ?>
                        </figcaption>
                    </figure>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
