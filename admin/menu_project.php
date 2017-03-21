<?php

/**
 * Display Administrative Menu for Projects.
 * @return menu
 */
function wpmanga_listProjects() {
	global $wpdb;
	
	// Display Specified Category
	if (isset($_GET['view']))
		$projects = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects` WHERE `category` = '%d' ORDER BY `title` ASC", (int) $_GET['view']));
	else
		$projects = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}projects` ORDER BY `title` ASC");
	
	if ($projects || isset($_GET['view'])) {
?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>Projets Manga <a href="?page=manga/project" class="add-new-h2">Nouveau Projet</a></h2>
			
			<ul class="subsubsub">
				<li class="all">
					<a href="admin.php?page=manga"<?php if (!isset($_GET['view'])) echo ' class="current"'; ?>>All <span class="count">(<?php echo count(get_sListProject()); ?>)</span></a>
				</li>
<?php
				$categories = get_sListCategories();
				foreach ($categories as $category) {
?>
				 | 
				<li class="">
					<a href="admin.php?page=manga&view=<?php echo $category->id; ?>"<?php if ($_GET['view'] == $category->id) echo ' class="current"'; ?>><?php echo $category->name; ?> <span class="count">(<?php echo get_sListCategory($category->id, false); ?>)</span></a>
				</li>
<?php
				}
?>
			</ul>
			
			<table class="wp-list-table widefat">
				<thead>
					<th scope="col" width="65px"></th>
					<th scope="col">Titre</th>
				</thead>
				
				<tfoot>
					<th scope="col" width="65px"></th>
					<th scope="col">Titre</th>
				</tfoot>
				
				<tbody id="the-list">
				<?php $row = 1; ?>
				<?php foreach ($projects as $project) { ?>
					<tr id="manga-<?php echo $project->id; ?>" <?php if ($row % 2) echo 'class="alternate" '; $row++ ?>valign="top">
						<td style="padding-bottom: 5px;">
							<img src="<?php echo get_sThumbnail('60x60', empty($project->image_thumbnail)?$project->image:$project->image_thumbnail ); ?>" style="padding: 1px; border: 1px double #878e98; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px;" width="60" height="60" alt="<?php the_title(); ?>" />
						</td>
						
						<td>
							<strong>
								<a href="admin.php?page=manga/project&action=edit&id=<?php echo $project->id; ?>" title="Éditer &#8220;<?php echo $project->title; ?>&#8220;"><?php echo $project->title; ?></a>
							</strong> <?php if ($project->author) echo 'par ' . $project->author; ?><br>
							Status: <?php echo get_sTitleCategory($project->category); ?>; Genre(s): <?php if ($project->genre) echo $project->genre; else echo "N/A";?>
							<div class="row-actions">
								<span class="edit">
									<a href="admin.php?page=manga/project&action=edit&id=<?php echo $project->id; ?>" title="Éditer ce Projet">Éditer</a>
								</span>
								 | 
								<span class="trash">
									<a href="admin.php?page=manga/project&action=delete&id=<?php echo $project->id; ?>" title="Effacer ce Projet">Effacer</a>
								</span>
								 | 
								<span class="view">
									<a href="<?php echo get_sPermalink($project); ?>" title="Afficher &#8220;<?php echo $project->title; ?>&#8221; Page du Projet">Afficher</a>
								</span>
							</div>
						</td>						
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
<?php
	} else {
?>
		<script type="text/javascript">
			location.replace("admin.php?page=manga/project")
		</script>
<?php
	}
}

/* EOF: admin/menu_project.php */