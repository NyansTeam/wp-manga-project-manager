<?php

/**
 * Adds the widgets
 * @return widget
 */
add_action('widgets_init', 'load_sWidgets');
function load_sWidgets() {
	unregister_widget('Latest_Releases_Widget');
	register_widget('Latest_Releases_Widget');
	unregister_widget('Chapters_Progress_Widget');
	register_widget('Chapters_Progress_Widget');
}


/**
 * wppb_init
 * loads the css and javascript
 * @author Chris Reynolds
 * @since 0.1
 */
add_action( 'init', 'chapterspb_init' );
function chapterspb_init() {
	$wppb_path = plugin_sURL();
	if ( !is_admin() ) { // don't load this if we're in the backend
		wp_register_style( 'wppb_css', $wppb_path . 'assets/wppb.css' );
	}
}


// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');
function enqueue_style(){
	if ( !is_admin() ) { // don't load this if we're in the backend
		wp_enqueue_style( 'wppb_css' );
   }
}


/**
 * Extends the widget class to include settings for latest releases.
 * @return widget
 */
class Latest_Releases_Widget extends WP_Widget {
	function Latest_Releases_Widget() {
		$widget_ops = array('classname' => 'latest-releases', 'description' => 'Affiche les dernières Releases (par date).');
		$control_ops = array('width' => 220, 'height' => 350, 'id_base' => 'latest-releases');
		$this->WP_Widget('latest-releases', 'Dernières Releases', $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title'] );
		$numofposts = $instance['numofposts'];
		if ($numofposts == "") $numofposts = 3;

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		} else {
			echo $before_title . __('Dernières Releases') . $after_title;
		}
		if (file_exists(get_sTemplate('widget-latest-releases.php'))) {
			include(get_sTemplate('widget-latest-releases.php'));
		}
		echo $after_widget;
	}

	function form($instance) {
		$numofposts = $instance['numofposts'];
		if ($numofposts == "") $numofposts = 3;
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Titre:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('numofposts'); ?>">Nombre de Releases à afficher:</label>
			<input type="text" size="3" id="<?php echo $this->get_field_id('numofposts'); ?>" name="<?php echo $this->get_field_name('numofposts'); ?>" value="<?php echo $numofposts; ?>" />
		</p>
	<?php
	}
}


/**
 * Extends the widget class to include settings for projects progress bar.
 * @return widget
 */
class Chapters_Progress_Widget extends WP_Widget {
	function Chapters_Progress_Widget() {
		$widget_ops = array('classname' => 'chapters-progress', 'description' => 'Affiche l\'avancement des chapitres.');
		$control_ops = array('width' => 220, 'height' => 350, 'id_base' => 'chapters-progress');
		$this->WP_Widget('chapters-progress', 'Avancement des chapitres', $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title'] );
		$projectCategory = $instance['projectCategory'];

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		} else {
			echo $before_title . __('Chapters Progress') . $after_title;
		}
		if (file_exists(get_sTemplate('widget-chapters-progress.php'))) {
			include(get_sTemplate('widget-chapters-progress.php'));
		}
		echo $after_widget;
	}

	function form($instance) {
		$projectCategory = $instance['projectCategory'];
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('projectCategory'); ?>">Afficher seulement les projets de cette catégorie (optionnel) :</label>
			
			<select name="<?php echo $this->get_field_name('projectCategory'); ?>" id="<?php echo $this->get_field_id('projectCategory'); ?>" >
				<?php
					$categories = get_sListCategories();
					echo "<option value=''>Aucune</option>";
					foreach ($categories as $category) {
						if ($projectCategory == $category->id)
							echo "<option value='{$category->id}' selected='selected'>{$category->name}</option>";
						else
							echo "<option value='{$category->id}'>{$category->name}</option>";
					}
				?>
			</select>
		</p>
	<?php
	}
}

/* EOF: includes/wp_widget.php */