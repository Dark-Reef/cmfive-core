<?php
echo "<h3>" . $user->getFullName() . " Settings</h3>";
?>

<div style="margin-top: 15px" class="tabs">
    <div class="tab-head">
        <a href="#general">General</a>
        <a href="#security">Security</a>
    </div>

    <div class="tab-body clearfix">
        <div id="general">
            <?php echo $w->partial("listUserGeneralSettings", $user->id) ?>
        </div>
        <div id="security">
            <?php echo $w->partial("listUserSecuritySettings", $user->id) ?>
        </div>
    </div>
</div>