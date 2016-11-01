<?php
/**
 * Bundle is not set up at all
 */
$this->extend('../setup');
$this->start('header');
?> 

    <div class="notice inline notice-error">
        <h3 class="has-icon">
            <?php esc_html_e('Unconfigured bundle','loco')?> 
        </h3>
        <p>
            <?php esc_html_e('This bundle isn\'t set up for translation in a way we understand','loco')?>.
            <?php esc_html_e('It needs configuring before you can do any translations','loco')?>.
        </p>
        <?php echo $this->render('inc-nav')?> 
    </div>