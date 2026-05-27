

<div class="container">

    <h1>Chat with User</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
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
            </table>
        </div>
    </div>
</div>
