<?php
/**
 * File info for a template file (POT)
 */
$this->extend('info');
$this->start('header');
?> 

    <div class="notice inline notice-info">
        <h3><?php esc_html_e('Template file','loco')?></h3>
        <dl>
            <dt><?php esc_html_e('File modified','loco')?>:</dt>
            <dd><?php $file->date('mtime')?></dd>

            <dt><?php esc_html_e('Last extracted','loco')?>:</dt>
            <dd><date><?php $params->date('potime')?></date></dd>
            
            <dt><?php esc_html_e('Source text','loco')?>:</dt>
            <dd><?php echo esc_html( $meta->getTotalSummary() )?></dd>
        </dl>
   </div>
    
    <?php 
    if( 'POT' !== $file->type && ! $params->isTemplate ):?> 
    <div class="notice inline notice-debug">
        <h3 class="has-icon">
            Unconventional file name
        </h3>
        <p>
            Template files should have the extension ".pot".<br />
            If this is intended to be a translation file it should end with a language code.
        </p>
    </div><?php
    endif; 