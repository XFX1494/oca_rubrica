<?php

function register_rlrubrica_settings() {
	//register our settings
	register_setting( 'rlrubrica-settings-group', 'rlrubrica_path_xml' );
	register_setting( 'rlrubrica-settings-group', 'rlrubrica_loading_report__from' );
	register_setting( 'rlrubrica-settings-group', 'rlrubrica_loading_report__to' );
	register_setting( 'rlrubrica-settings-group', 'rlrubrica_loading_report__subject' );
	register_setting( 'rlrubrica-settings-group', 'rlrubrica_template_report' );
}

function rlrubrica_settings_page() {
?>
<div class="wrap">
<h1>Impostazioni</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'rlrubrica-settings-group' ); ?>
    <?php do_settings_sections( 'rlrubrica-settings-group' ); ?>
    <table class="form-table">

        
    
        <tr valign="top">
        <th scope="row">Percorso XML della rubrica</th>
        <td><input type="text" name="rlrubrica_path_xml" value="<?php echo esc_attr( get_option('rlrubrica_path_xml') ); ?>" style="width: 500px;" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Mittente report Rubrica</th>
        <td><input type="text" name="rlrubrica_loading_report__from" value="<?php echo esc_attr( get_option('rlrubrica_loading_report__from') ); ?>" style="width: 300px;" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Destinatario report Rubrica</th>
        <td><input type="text" name="rlrubrica_loading_report__to" value="<?php echo esc_attr( get_option('rlrubrica_loading_report__to') ); ?>" style="width: 300px;" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Oggetto report Rubrica</th>
        <td><input type="text" name="rlrubrica_loading_report__subject" value="<?php echo esc_attr( get_option('rlrubrica_loading_report__subject') ); ?>" style="width: 300px;" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Template del Report</th>
        <td><textarea name="rlrubrica_template_report" style="width: 600px; height: 200px;float: left;margin-right: 5px;"><?php echo esc_attr( get_option('rlrubrica_template_report') ); ?></textarea>
        <i>utilizzare <strong>%dati%</strong> per inserire la lista degli utenti importati</i>
        </td>
        </tr>
        
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php }
?>