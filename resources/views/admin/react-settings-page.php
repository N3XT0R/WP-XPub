<div id="root"></div>
<script>
window.xpubSettings = {
    restUrl: <?php echo json_encode(rest_url()); ?>,
    restNonce: <?php echo json_encode(wp_create_nonce('wp_rest')); ?>
};
</script>
