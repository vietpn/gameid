<?php if (!empty($messages)) { ?>
    <div class="alert alert-danger">
        <ul>
            <?php if (is_array($messages)) { ?>
                <?php foreach ($messages as $field => $errors) {
                    foreach($errors as $key => $val){
                        echo '<li>' . $val . '</li>';
                    }
                } ?>
            <?php } else { ?>
                <li><?php echo $messages; ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } else { ?>
    <div class="alert alert-success">
        <ul>
            <li>Success</li>
        </ul>
    </div>
<?php } ?>
