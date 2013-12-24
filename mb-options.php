<?php
// create custom plugin settings menu
add_action('admin_menu', 'mb_create_menu');

function mb_create_menu() {

	//create new top-level menu
	add_menu_page('MB Feeder Settings', 'MB Feeder Settings', 'administrator', __FILE__, 'mb_settings_page',plugins_url('/images/mba.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'mb-settings-group', 'mb_unique_id' );
	register_setting( 'mb-settings-group', 'mb_read_more' );
	register_setting( 'mb-settings-group', 'mb_reg_domain' );
	register_setting( 'mb-settings-group', 'mb_max_num' );
}

function mb_settings_page() {
?>
<div class="wrap">
<img src="<?php echo plugins_url('images/High_res_mibiz_logo.png', __FILE__); ?>" width="50" height="54" style="float: left; margin-right: 20px;"><h2>Feeder Setup</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'mb-settings-group' ); ?>
    <?php do_settings_sections( 'mb-settings-group' ); ?>
    <table class="form-table">
    
        <tr valign="top">
        <th scope="row"><h3 style="border-bottom: solid 1px #000;">General Settings</h3></th>
        </tr>
        
        <tr valign="top">
        <th scope="row">Feed Unique ID</th>
        <td><input type="text" name="mb_unique_id" value="<?php echo get_option('mb_unique_id'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Text for "Continue Reading" <em>(default is "Read More")</em></th>
        <td><input type="text" name="mb_read_more" value="<?php echo get_option('mb_read_more'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Your registered domain</th>
        <td><input type="text" name="mb_reg_domain" value="<?php echo get_option('mb_reg_domain'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><h3 style="border-bottom: solid 1px #000;">Shortcode Settings</h3></th>
        </tr>
        
        <tr valign="top">
        <th scope="row">Maximum Feeds displayed</th>
        <td>
            <select id="mb_max_num" name="mb_max_num">
            	<?php
				$options = array(1,2,3,4,5,6,7,8,9,10);
				$value = get_option('mb_max_num');
				foreach($options as $option):
					if($value == $option):
				?>
                	<option value="<?=$option?>" selected="selected"><?=$option?></option>
                 	<?php else: ?>
                    <option value="<?=$option?>"><?=$option?></option>
                <?php endif; endforeach;?>
            </select>
        </td>
        </tr>
        
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>