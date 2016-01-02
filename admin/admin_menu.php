<?php

/**
 * Include Core WP Admin Files
 */
include('menu_project.php');
include('data_project.php');
include('menu_release.php');
include('data_release.php');
include('menu_cover.php');
include('data_cover.php');

/**
 * Generate WP Admin Menu
 * @return menu
 */
function wpmanga_adminmenu () {
	if ( current_user_can('edit_posts') || current_user_can('edit_pages') ) {

		// Projects
		add_menu_page('Projets', 'Projets', 'edit_posts', 'manga', 'wpmanga_listProjects');
		add_submenu_page('manga', 'Ajouter un projet', '-- Ajouter un projet', 'edit_posts', 'manga/project', 'wpmanga_dataProject');

		// Volume Covers
		add_submenu_page('manga', 'Couvertures de Tome', 'Couvertures de Tome', 'edit_posts', 'manga/list/volume', 'wpmanga_listCovers');
		add_submenu_page('manga', 'Ajout de couverture', '-- Ajout de couverture', 'edit_posts', 'manga/volume', 'wpmanga_dataCover');

		// Releases
		add_submenu_page('manga', 'Releases', 'Releases', 'edit_posts', 'manga/list/release', 'wpmanga_listReleases');
		add_submenu_page('manga', 'Ajouter une release', '-- Ajouter une release', 'edit_posts', 'manga/release', 'wpmanga_dataRelease');

		// Miscellaneous Pages
		if (is_admin())
			add_submenu_page('manga', 'WP Manga Settings', 'Settings', 'manage_options', 'manga/settings', 'wpmanga_settings');
		add_submenu_page('manga', 'A propos', 'A propos', 'edit_posts', 'manga/about', 'wpmanga_about');


		// Load Required JavaScript and StyleSheet
		if (preg_match("/(manga\/project|manga\/volume)/i", $_GET['page'])) {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('jquery');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('pimage-upload', plugin_sURL() . 'admin/assets/media-uploader.js', array('jquery', 'media-upload', 'thickbox'));
		}

		if (preg_match("/(manga\/release)/i", $_GET['page'])) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui', plugin_sURL() . 'admin/assets/jquery-ui.custom.js', array('jquery'));
			wp_enqueue_script('datetime', plugin_sURL() . 'admin/assets/jquery-ui.datetime.js', array('jquery'));
			wp_enqueue_style('datetime', plugin_sURL() . 'admin/assets/jquery-ui.datetime.css');
			wp_enqueue_style('jquery-ui', plugin_sURL() . 'admin/assets/jquery-ui.custom.css');
		}
	}
}

/**
 * Generate WP Admin Menu for Admin Bar
 * @return menu
 */
add_action('admin_bar_menu', 'wpmanga_adminbar', 9999);
function wpmanga_adminbar() {
	global $wp_admin_bar;
	if (!is_admin_bar_showing()) return;

	if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga',
			'title' => 'WPManga',
			'href' => FALSE
		));

		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_project_list',
			'parent' => 'wpmanga',
			'title' => 'Projets',
			'href' => admin_url('admin.php?page=manga')
		));

		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_project',
			'parent' => 'wpmanga',
			'title' => 'Ajouter un nouveau projet',
			'href' => admin_url('admin.php?page=manga/project')
		));

		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_release_list',
			'parent' => 'wpmanga',
			'title' => 'Releases',
			'href' => admin_url('admin.php?page=manga/list/release')
		));

		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_release',
			'parent' => 'wpmanga',
			'title' => 'Ajouter une Release',
			'href' => admin_url('admin.php?page=manga/release')
		));
	}

	if (current_user_can('level_10')) {
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_settings',
			'parent' => 'wpmanga',
			'title' => 'Paramètres',
			'href' => admin_url('admin.php?page=manga/settings')
		));
	}
}

/**
 * Generate Plugin Admin Settings Link
 * @return menu
 */
add_filter('plugin_action_links', 'wpmanga_settings_link', 10, 2);
function wpmanga_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(plugin_sDIR() . '/wpmanga.php');

	if (is_admin() && $file == $this_plugin) {
		$settings_link = '<a href="admin.php?page=manga/settings">Paramètres</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}


function wpmanga_settings() {
	global $wpdb;

	if (isset($_POST['settings_nonce'])) {
		if ( !wp_verify_nonce( $_POST['settings_nonce'], plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ) ) {
			echo '<div class="error"><p>Erreur: Vérification de Sécurité échouée.</p></div>';
		} else {
			$_POST = array_map('trim', $_POST);

			// Check Boxes Suck (Search for Alternative Method)
			if (!$_POST['wpmanga_releasebar_style']) $_POST['wpmanga_releasebar_style'] = 0;
			if (!$_POST['wpmanga_page_details_title']) $_POST['wpmanga_page_details_title'] = 0;
			if (!$_POST['wpmanga_page_details_header']) $_POST['wpmanga_page_details_header'] = 0;
			if (!$_POST['wpmanga_foolreader']) $_POST['wpmanga_foolreader'] = 0;
			if (!$_POST['wpmanga_widget_icons']) $_POST['wpmanga_widget_icons'] = 0;
			if (!$_POST['wpmanga_delay_megaupload']) $_POST['wpmanga_delay_megaupload'] = 0;
			if (!$_POST['wpmanga_delay_mediafire']) $_POST['wpmanga_delay_mediafire'] = 0;
			if (!$_POST['wpmanga_delay_depositfiles']) $_POST['wpmanga_delay_depositfiles'] = 0;
			if (!$_POST['wpmanga_delay_fileserve']) $_POST['wpmanga_delay_fileserve'] = 0;
			if (!$_POST['wpmanga_delay_filesonic']) $_POST['wpmanga_delay_filesonic'] = 0;
			if (!$_POST['wpmanga_delay_pdf']) $_POST['wpmanga_delay_pdf'] = 0;
			if (!$_POST['wpmanga_disable_megaupload']) $_POST['wpmanga_disable_megaupload'] = 0;
			if (!$_POST['wpmanga_disable_mediafire']) $_POST['wpmanga_disable_mediafire'] = 0;
			if (!$_POST['wpmanga_disable_depositfiles']) $_POST['wpmanga_disable_depositfiles'] = 0;
			if (!$_POST['wpmanga_disable_fileserve']) $_POST['wpmanga_disable_fileserve'] = 0;
			if (!$_POST['wpmanga_disable_filesonic']) $_POST['wpmanga_disable_filesonic'] = 0;
			if (!$_POST['wpmanga_disable_pdf']) $_POST['wpmanga_disable_pdf'] = 0;

			//Url and Title not empty
			if (!$_POST['wpmanga_projectslist_url']) {
				$_POST['wpmanga_projectslist_url'] = 'projects';
			}
			else {
				$_POST['wpmanga_projectslist_url'] = get_sSanitizedSlug($_POST['wpmanga_projectslist_url']);
			}
			if (!$_POST['wpmanga_projectslist_title']) $_POST['wpmanga_projectslist_title'] = 'Projects';

			// Filter $_POST and Update Setting
			$_DATA = array();
			foreach ($_POST as $key => $value) {
				if (preg_match("/wpmanga_(.*?)/i", $key)) {
					$status = wpmanga_set($key, $value); $_DATA[$key] = $status;
				}

			}

			// Update Thumbnails
			if ($_DATA['wpmanga_thumbnail_list_width'] || $_DATA['wpmanga_thumbnail_list_height']) {
				set_time_limit(0);
				$thumbnail = new WP_Http;
				foreach (get_sListProject() as $project) {
					$thumbnail->request(plugin_sURL() . 'includes/generate_thumbnail.php?src=' . $project->image . '&w=' . wpmanga_get('wpmanga_thumbnail_list_width', 145) . '&h=' . wpmanga_get('wpmanga_thumbnail_list_height', 300));
				}
			}

			echo '<div class="updated"><p>Paramètres mis à jour.</p></div>';
		}
	}

	if (isset($_GET['generate'])) {
		if ($_GET['generate'] == 'thumbnails') {
			set_time_limit(0);
			$thumbnail = new WP_Http;
			foreach (get_sListProject() as $project) {
				$thumbnail->request(plugin_sURL() . 'includes/generate_thumbnail.php?src=' . $project->image . '&w=' . wpmanga_get('wpmanga_thumbnail_list_width', 145) . '&h=' . wpmanga_get('wpmanga_thumbnail_list_height', 300));
			}

			echo '<div class="updated"><p>Génération de miniatures terminée.</p></div>';
		}
	}
?>
	<div class="wrap">
		<?php screen_icon('options-general'); ?>
		<h2>Paramètres de WP Manga</h2>

		<p>WP Manga Project Manager possède plusieurs options affectant le comportement du plugin dans different endroits. Les options Frontend gèrent tout ce qui est visuel ainsi que les options disponibles dans les pages, posts, ou les widgets. Les options Backend contrôlent la zone admin du plugin.</p>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<form method="post" action="admin.php?page=manga/settings">
					<div class="postbox">
						<h3 class='hndle'><span>Options Frontend</span></h3>
						<div class="inside">
							<table class="form-table fixed">
								<tr class="form-field">
									<td width="250px"><label for="wpmanga_thumbnail_list_width">Dimensions de miniature</label></td>
									<td>
										Largeur <input name="wpmanga_thumbnail_list_width" id="wpmanga_thumbnail_list_width" type="number" value="<?php echo wpmanga_get('wpmanga_thumbnail_list_width', 145); ?>" style="width: 10%;"> &nbsp;
										Hauteur <input name="wpmanga_thumbnail_list_height" id="wpmanga_thumbnail_list_height" type="number" value="<?php echo wpmanga_get('wpmanga_thumbnail_list_height', 300); ?>" style="width: 10%;"> &nbsp; <a class="button-secondary" href="admin.php?page=manga/settings&generate=thumbnails">Forcer la génération de mniiatures</a>
									</td>
								</tr>

								<tr class="form">
									<td valign="top" width="250px"><label>Page des projets individuels</label></td>
									<td>
										<input type="checkbox" name="wpmanga_page_details_title" id="wpmanga_page_details_title" value="1" <?php if (wpmanga_get('wpmanga_page_details_title', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_page_details_title">Désactiver le filtre titre <span class="description">(Pour des thèmes spécifiques)</span></label> <br>
										<input type="checkbox" name="wpmanga_page_details_header" id="wpmanga_page_details_header" value="1" <?php if (wpmanga_get('wpmanga_page_details_header', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_page_details_header">Afficher en-tête <span class="description">(Pour des thèmes spécifiques)</span></label>
									</td>
								</tr>

								<tr class="form">
									<td valign="top" width="250px"><label>Générateur de lien LeL</label></td>
									<td>
										<input name="wpmanga_reader" id="reader_foolreader" type="radio" value="1"<?php if (wpmanga_get('wpmanga_reader', 1) == 1) echo ' checked="checked"'; ?>> <label for="reader_foolreader">FoOlSlide</label> &nbsp;
										<input name="wpmanga_reader" id="reader_none" type="radio" value="0"<?php if (!wpmanga_get('wpmanga_reader', 1) == 0) echo ' checked="checked"'; ?>> <label for="reader_none">Aucun</label>
									</td>
								</tr>

								<tr class="form">
									<td valign="top" width="250px"><label for="wpmanga_releasebar_style">Style d'affichage de la barre des releases</label></td>
									<td>
										<select name="wpmanga_releasebar_style" id="wpmanga_releasebar_style" style="width: 100%">
											<option value="1"<?php if (wpmanga_get('wpmanga_releasebar_style', 1) == '1') echo ' selected="selected"'; ?>>Barre de release par défaut</option>
											<option value="2"<?php if (wpmanga_get('wpmanga_releasebar_style', 1) == '2') echo ' selected="selected"'; ?>>Barre de release basique</option>
										</select>
									</td>
								</tr>

								<tr class="form">
									<td width="250px"><label for="wpmanga_widget_icons">Widget</label></td>
									<td>
										<input type="checkbox" name="wpmanga_widget_icons" id="wpmanga_widget_icons" value="1" <?php if (wpmanga_get('wpmanga_widget_icons', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_widget_icons">Afficher les icones des releases</label>
									</td>
								</tr>

								<tr class="form">
									<td width="250px"><label for="wpmanga_channel">Channel IRC</label></td>
									<td>
										<input name="wpmanga_channel" id="wpmanga_channel" type="text" placeholder="irc://irc.irchighway.net/beta" value="<?php echo wpmanga_get('wpmanga_channel', ''); ?>" style="width: 100%;">
									</td>
								</tr>

								<tr class="form">
									<td width="250px"><label for="wpmanga_projectslist_url">URL de la liste des projets/label></td>
									<td>
										<input name="wpmanga_projectslist_url" id="wpmanga_projectslist_url" type="text" value="<?php echo wpmanga_get('wpmanga_projectslist_url', 'projets'); ?>" style="width: 100%;">
									</td>
								</tr>

								<tr class="form">
									<td width="250px"><label for="wpmanga_projectslist_title">Titre de la liste des projets</label></td>
									<td>
										<input name="wpmanga_projectslist_title" id="wpmanga_projectslist_title" type="text" value="<?php echo wpmanga_get('wpmanga_projectslist_title', 'Projets'); ?>" style="width: 100%;">
									</td>
								</tr>

								<tr class="form-field">
									<td valign="top" style="padding-top: 10px;" width="250px"><label for="wpmanga_delay">Lien de téléchargement retardé</label></td>
									<td>
										<input name="wpmanga_delay" id="wpmanga_delay" type="number" value="<?php echo wpmanga_get('wpmanga_delay', 0); ?>" style="width: 10%;"> Heures <br>
										<input type="checkbox" name="wpmanga_delay_depositfiles" id="wpmanga_delay_depositfiles" value="1" <?php if (wpmanga_get('wpmanga_delay_depositfiles', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_depositfiles">Deposit Files</label> <br>
										<input type="checkbox" name="wpmanga_delay_fileserve" id="wpmanga_delay_fileserve" value="1" <?php if (wpmanga_get('wpmanga_delay_fileserve', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_fileserve">FileServe</label> <br>
										<input type="checkbox" name="wpmanga_delay_filesonic" id="wpmanga_delay_filesonic" value="1" <?php if (wpmanga_get('wpmanga_delay_filesonic', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_filesonic">FileSonic</label> <br>
										<input type="checkbox" name="wpmanga_delay_mediafire" id="wpmanga_delay_mediafire" value="1" <?php if (wpmanga_get('wpmanga_delay_mediafire', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_mediafire">MediaFire</label> <br>
										<input type="checkbox" name="wpmanga_delay_megaupload" id="wpmanga_delay_megaupload" value="1" <?php if (wpmanga_get('wpmanga_delay_megaupload', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_megaupload">MEGA</label> <br>
										<input type="checkbox" name="wpmanga_delay_pdf" id="wpmanga_delay_pdf" value="1" <?php if (wpmanga_get('wpmanga_delay_pdf', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_pdf">PDF</label>
									</td>
								</tr>
							</table>

							&nbsp; <input type="submit" class="button-primary" name="save" value="Enregistrer les paramètres"><br><br>
							<input type="hidden" name="settings_nonce" value="<?php echo wp_create_nonce( plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ); ?>">
						</div>
					</div>

					<div class="postbox" >
						<h3 class='hndle'><span>Options Backend</span></h3>
						<div class="inside">
							<table class="form-table fixed">
								<tr class="form-field">
									<td valign="top" style="padding-top: 10px;" width="250px"><label>Désactiver les services de téléchargement</label></td>
									<td>
										<input type="checkbox" name="wpmanga_disable_depositfiles" id="wpmanga_disable_depositfiles" value="1" <?php if (wpmanga_get('wpmanga_disable_depositfiles', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_depositfiles">Deposit Files</label> <br>
										<input type="checkbox" name="wpmanga_disable_fileserve" id="wpmanga_disable_fileserve" value="1" <?php if (wpmanga_get('wpmanga_disable_fileserve', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_fileserve">FileServe</label> <br>
										<input type="checkbox" name="wpmanga_disable_filesonic" id="wpmanga_disable_filesonic" value="1" <?php if (wpmanga_get('wpmanga_disable_filesonic', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_filesonic">FileSonic</label> <br>
										<input type="checkbox" name="wpmanga_disable_mediafire" id="wpmanga_disable_mediafire" value="1" <?php if (wpmanga_get('wpmanga_disable_mediafire', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_mediafire">MediaFire</label> <br>
										<input type="checkbox" name="wpmanga_disable_megaupload" id="wpmanga_disable_megaupload" value="1" <?php if (wpmanga_get('wpmanga_disable_megaupload', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_megaupload">MEGA</label> <br>
										<input type="checkbox" name="wpmanga_disable_pdf" id="wpmanga_disable_pdf" value="1" <?php if (wpmanga_get('wpmanga_disable_pdf', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_pdf">PDF</label>
									</td>
								</tr>

								<tr class="form">
									<td width="250px"><label for="wpmanga_release_statuspublished">Status de publication de releases (0 pour publication auto)</label></td>
									<td>
										<input name="wpmanga_release_statuspublished" id="wpmanga_release_statuspublished" type="text" value="<?php echo wpmanga_get('wpmanga_release_statuspublished', ''); ?>" style="width: 100%;">
									</td>
								</tr>
							</table>

							&nbsp; <input type="submit" class="button-primary" name="save" value="Enregistrer les paramètres"><br><br>
							<input type="hidden" name="settings_nonce" value="<?php echo wp_create_nonce( plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}


function wpmanga_about() {
	global $wpdb;
?>
	<div class="wrap">
		<?php screen_icon('users'); ?>
		<h2><?php echo esc_html( 'A propos' ); ?></h2>

		<br />
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div class="postbox" >
					<h3 class='hndle'><span>A quoi sert ce plugin?</span></h3>
					<div class="inside">
						<p>WP Manga Project Manager vous permet de gérer vos projets et releases afin de s'assurer que toute les information et liens sont bons et fonctionnels a travers tout WordPress. Cela permet a vos utilisateurs d'éviter toute confusion et conflit d'information en délivrant toute les informations dans une seule base de donnée.</p>
					</div>
				</div>

				<div class="postbox" >
					<h3 class='hndle'><span>Usage</span></h3>
					<div class="inside">
						<p>We are currently drafting a documentation regarding how to use this plugin. It was meant to be used by scanlation groups to keep information about their releases updated.</p>
					</div>
				</div>

				<div class="postbox" >
					<h3 class='hndle'><span>Help and Support</span></h3>
					<div class="inside">
						<p>Support is provided through IRC. Please contact me (prinny) on the IRCHighway network in #beta.</p>
					</div>
				</div>

				<div class="postbox" >
					<h3 class='hndle'><span>Author and License</span></h3>
					<div class="inside">
						<p>This plugin was written by prinny. It is licensed as Free Software under GPL v2.<br />
						If you like this plugin, please send a donation. This will allow me to further develop the plugin and to provide countless hours of support in the future. Any amount is appreciated!</p>
					</div>
				</div>

				<div class="postbox" >
					<h3 class='hndle'><span>Credits and Thanks</span></h3>
					<div class="inside">
						<p>
							Many thanks for those groups and users that help with the testing of this plugin and providing suggestions to improve it as well.<br /><br />
							Scanlation Groups:<br />
							- Sense Scans<br />
							- Kirei Cake<br />
							- Extras<br />
							- FTH Scans
							<br /><br />
							Members/Users:<br />
							- busaway<br />
							- Empathy<br />
							- Lollipop<br />
							- Zyki
						</p>
					</div>
				</div>
				<?php if (current_user_can('manage_options')) { ?>
				<div class="postbox" >
					<h3 class='hndle'><span>Debug and Version Information</span></h3>
					<div class="inside">
						<p>The following will provide you with information regarding software versions. <b>This information must be provided in bug reports.</b><br /><br />
						- Manga Projects (Plugin): <?php echo get_sVersion('plugin'); ?><br />
						- Manga Projects (Database): <?php echo get_sVersion('db'); ?><br />
						- WordPress: <?php echo get_bloginfo('version'); ?><br />
						- PHP: <?php echo phpversion(); ?><br />
						- MySQL Server: <?php echo mysql_get_server_info(); ?>
						</p>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php
}

/* EOF: admin/admin_menu.php */