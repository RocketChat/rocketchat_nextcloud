<?php
script('rocketchat_nextcloud', 'chat');
style('rocketchat_nextcloud', 'style');
?>

<?php if (isset($_['new']) && $_['new'] === '1') : ?>
    <div class="messenger--add-members-info"> Add members to discussion by clicking the members button. </div>
<?php endif; ?>

<?php if (isset($_['token'])) : ?>
    <input type="hidden" name="rocketchat_token" value="<?php p($_['token']); ?>"/>
<?php endif; ?>

<iframe id="rocket-chat-iframe" src="<?php p($_['url']); ?>" allowfullscreen></iframe>
