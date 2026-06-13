<div class="container">
    <h1>My Image Gallery</h1>

    <div class="box">
        <h3>Upload new image</h3>
        <form action="<?php echo Config::get('URL'); ?>gallery/upload" method="post" enctype="multipart/form-data">
            <input type="file" name="image" required />
            <button type="submit">Upload Now</button>
        </form>
    </div>

    <div class="box">
        <h3>Images</h3>
        <ul class="thumbnail-grid">
            <?php foreach ($this->images as $image) { ?>
                <li>
                    <div class="thumbnail-item">
                        <div class="thumb-frame">
                            <img src="<?php echo Config::get('URL'); ?>gallery/show/<?php echo $image->image_id; ?>">
                        </div>
                        <div class="button-group">
                            <a href="<?php echo Config::get('URL'); ?>gallery/download/<?php echo $image->image_id; ?>" class="btn">Download</a>
                            <?php if ($image->owner_id == Session::get('user_id')) { ?>
                                <a href="<?php echo Config::get('URL'); ?>gallery/toggle/<?php echo $image->image_id; ?>" class="btn">
                                    <?php echo ($image->is_shared == 1 ? "Make Private" : "Share"); ?>
                                </a>
                                <a href="<?php echo Config::get('URL'); ?>gallery/delete/<?php echo $image->image_id; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php } else { ?>
                                <span class="public-tag">Public Image</span>
                            <?php } ?>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<style>
/* Simple Thumbnail Grid */
.thumbnail-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    list-style: none;
    padding: 0;
}

.thumbnail-item {
    width: 180px;
    border: 1px solid #ddd;
    background: #fff;
    padding: 5px;
}

.thumb-frame {
    height: 120px;
    overflow: hidden;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.thumb-frame img {
    min-height: 100%;
    min-width: 100%;
    object-fit: cover;
}

/* Zoom effect on hover */
.thumb-frame img:hover {
    transform: scale(1.4);
    transition: transform 0.4s;
}

/* Button layout */
.button-group {
    display: flex;
    flex-direction: column;
    gap: 3px;
    margin-top: 5px;
}

.btn {
    display: block;
    text-align: center;
    padding: 4px;
    background-color: #f4f4f4;
    border: 1px solid #ccc;
    color: #333;
    text-decoration: none;
    font-size: 11px;
}

.btn:hover {
    background-color: #e0e0e0;
}

.btn-delete {
    color: #c00;
}

.btn-delete:hover {
    background-color: #fee;
}

.public-tag {
    display: block;
    text-align: center;
    font-size: 10px;
    color: #999;
    padding: 4px;
}
</style>
