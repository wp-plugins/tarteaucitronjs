<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Admin
if(!class_exists('tarteaucitron_Admin'))
{
	class tarteaucitron_Admin {
        
		var $hook = 'tarteaucitronjs';
		var $longname = 'tarteaucitron.js';
		var $shortname = 'tarteaucitron.js';
		var $filename = 'tarteaucitronjs/tarteaucitron.php';
		var $homepage = 'https://opt-out.ferank.eu/';

		// Ajout de la page des réglages et test config
		function tarteaucitron_Admin() {
            add_action('admin_menu', array(&$this, 'register_settings_page'));
			add_filter('plugin_action_links', array(&$this,'add_action_link'), 10, 2);
			add_action('admin_init', array(&$this,'tarteaucitron_register'));
			if(get_option('tarteaucitronUUID') == '') {
                add_action('admin_notices', array(&$this,'tarteaucitron_admin_notices'));
            }
		}
		
		// Enregistrement des options
		function tarteaucitron_register() {
			register_setting( 'tarteaucitron', 'tarteaucitronUUID' );
			register_setting( 'tarteaucitron', 'tarteaucitronToken' );
		}
		
		// Ajout de la page des réglages
		function register_settings_page() {
			$hook_suffix = add_options_page($this->longname, $this->shortname, 'manage_options', $this->hook, array(&$this,'tarteaucitron_config_page'));
			add_action('load-' . $hook_suffix , array(&$this,'tarteaucitron_load_function'));
		}
		
		// Suppression de l'alerte
		function tarteaucitron_load_function() {
			remove_action('admin_notices', array(&$this,'tarteaucitron_admin_notices'));
		}
		
		// Alerte
		function tarteaucitron_admin_notices() {
			echo "<div id='notice' class='updated fade'><p>".__('Le module tarteaucitron.js a été correctement installé !', 'tarteaucitron')." <a href='" . $this->plugin_options_url() . "'>".__('Configurer le module', 'tarteaucitron')."</a></p></div>\n";
		}
		
		// Page des réglages
		function tarteaucitron_config_page() {
			settings_fields( 'tarteaucitron' );
            
            if(get_option('tarteaucitronUUID') != '' OR get_option('tarteaucitronToken') != '') {
                if(tarteaucitron_post('check=1') == 0) {
                    update_option('tarteaucitronUUID', '');
                    update_option('tarteaucitronToken', '');
                }
            }
            
            if($_POST['tarteaucitronEmail'] != '' AND $_POST['tarteaucitronPass'] != '') {
                $result = tarteaucitron_post('login=1&email='.$_POST['tarteaucitronEmail'].'&pass='.$_POST['tarteaucitronPass'].'&website='.$_SERVER['SERVER_NAME'], 0);
                if($result != '0') {
                    $ret = explode('=', $result);
                    update_option('tarteaucitronUUID', $ret[0]);
                    update_option('tarteaucitronToken', $ret[1]);
                }
            } elseif (isset($_POST['tarteaucitronLogout'])) {
                tarteaucitron_post('remove=1');
                update_option('tarteaucitronUUID', '');
                update_option('tarteaucitronToken', '');
            } elseif(isset($_POST['tarteaucitron_send_services_static']) AND $_POST['wp_tarteaucitron__service'] != '') {
                $service = $_POST['wp_tarteaucitron__service'];
                  $r = 'service='.$service.'&configure_services='.$_POST['wp_tarteaucitron__configure_services'].'&';
                  foreach ($_POST as $key => $val) {
                      if (preg_match('#^wp_tarteaucitron__'.$service.'#', $key)) {
                          $r .= preg_replace('#^wp_tarteaucitron__#', '', $key).'='.$val.'&';
                      }
                  }
                  tarteaucitron_post(trim($r, '&'));
              }
                
            if(get_option('tarteaucitronUUID') == '') {
                echo '<div class="wrap">
                <h1>tarteaucitron.js</h1>
				<form method="post" action="">
                    <div class="tarteaucitronDiv" style="margin-bottom:25px;">
                        <p><b><a href="https://opt-out.ferank.eu/pro/#paiement" target="_blank">'.__('Avant d\'utiliser les services tarteaucitron, vous devez souscrire à un abonnement.', 'tarteaucitron').'</a></b></p>
                    </div>
                    <h2 style="margin-bottom:20px">'.__('Connexion', 'tarteaucitron').'</h2>
                    <div class="tarteaucitronDiv">
                        <table class="form-table">
				            <tr valign="top">
                                <th scope="row">'.__('Email', 'tarteaucitron').'</th>
                                <td><input type="text" name="tarteaucitronEmail" /></td>
                            </tr>
				            <tr valign="top">
                                <th scope="row">'.__('Mot de passe', 'tarteaucitron').'</th>
                                <td><input type="password" name="tarteaucitronPass" /></td>
                            </tr>
				            <tr valign="top">
                                <th scope="row">&nbsp;</th>
                                <td><input type="submit" /></td>
                            </tr>
                        </table>
                    </div>
				</form>
                </div>
                <style type="text/css">.tarteaucitronDiv{background:#FFF;padding: 10px;border: 1px solid #eee;border-bottom: 2px solid #ddd;max-width: 500px;}</style>';
            } else {
                $abo = tarteaucitron_post('abonnement=1');
                
                if($abo > time()) {
                    $abonnement = '<strong style="color:darkgreen">'.__('Abonnement valide jusqu\'au', 'tarteaucitron').' '.date('d/m/Y H:i', $abo).'</b>';
                } else {
                    $abonnement = '<strong style="color:darkred">'.__('Abonnement expiré !', 'tarteaucitron').' <a target="_blank" href="https://opt-out.ferank.eu/pro/#paiement">'.__('Recharger', 'tarteaucitron').'</a>';
                }
                echo '<div class="wrap">
				<form method="post" action="">
                    <input type="hidden" name="tarteaucitronLogout" />
                    <div class="tarteaucitronDiv">
                        <table class="form-table" style="margin:0 !important">
				            <tr valign="top">
                                <td>'.$abonnement.'</td>
                            </tr>
				            <tr valign="top">
                                <td><input type="submit" value="'.__('Déconnexion', 'tarteaucitron').'" style="margin: 0;padding: 5px;font-size: 12px;" /></td>
                            </tr>
                        </table>
                    </div>
				</form>
                <div class="tarteaucitronDiv" style="margin-bottom: 120px;max-width:600px;padding:20px;background:#fff;margin-top:20px">
                    '.tarteaucitron_post('getForm=1').'
                </div>
                </div>
                <style type="text/css">.tarteaucitronDiv{background:#FFF;padding: 10px;border: 1px solid #eee;border-bottom: 2px solid #ddd;max-width: 500px;}</style>';
            }
		}
		
		// Liens vers les réglages
		function plugin_options_url() {
			return admin_url('options-general.php?page='.$this->hook);
		}
		
		// Liens vers les réglages depuis la page des extensions
		function add_action_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) $this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Réglages', 'tarteaucitron') . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
	}   
}
$tarteaucitron_admin = new tarteaucitron_Admin();

/**
 * CSS et Javascript
 */
function tarteaucitron_admin_css() {
	wp_register_style('tarteaucitron', plugins_url('tarteaucitronjs/css/admin.css'));

    wp_enqueue_style('tarteaucitron');
    wp_enqueue_script('tarteaucitron', plugins_url('tarteaucitronjs/js/admin.js'));
}
add_action('admin_enqueue_scripts', 'tarteaucitron_admin_css');