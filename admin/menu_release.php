<?php

/**
 * Display Administrative Menu for Releases.
 * @return menu
 */
function wpmanga_listReleases() {
	global $wpdb;
	
	$projects = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}projects` ORDER BY `title` ASC");
	
	$publishedstatus = wpmanga_get('wpmanga_release_statuspublished',0);
	if ($projects) {
?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>Releases <a href="?page=manga/release" class="add-new-h2">Ajouter une Release</a></h2>
			

		<script type="text/javascript">
			function toggleFinishedRelease(cb) {
			  if (cb.checked)
				jQuery('.finished').css( "display", "table-row" );
			  else
				jQuery('.finished').css( "display", "none" );
			}

			function catFilter(cbo) {
			  var category = cbo.options[cbo.selectedIndex].value;
			  if (category != '')
			  {
				jQuery('.project').css( "display", "none" );
				jQuery('.cat'+category).css( "display", "block" );
			  }
			  else
			  {
				jQuery('.project').css( "display", "block" );
			  }
			}

			function projectFilter(cbo) {
			  var project = cbo.options[cbo.selectedIndex].value;
			  if (project != '')
			  {
				jQuery('.project').css( "display", "none" );
				jQuery('#proj'+project).css( "display", "block" );
			  }
			  else
			  {
				jQuery('.project').css( "display", "block" );
			  }
				document.getElementById("categoryCombo").selectedIndex=0;
			}

			jQuery(function($){ // DOM is now read and ready to be manipulated
				jQuery('.finished').css( "display", "none" );
				document.getElementById("categoryCombo").selectedIndex=1;
				catFilter(document.getElementById("categoryCombo"));
			});
		</script>

<?php if ($publishedstatus != 0)
	  {
?>
		<label><input type="checkbox" onclick="toggleFinishedRelease(this);">Afficher les releases terminées</label><br />
<?php } ?>
		<span>Afficher seulement les projets de la catégorie </span>
		<select name="category" id="categoryCombo" onchange="catFilter(this)" >
				<?php
					$categories = get_sListCategories();
					echo "<option value='' selected=\"selected\">Tout</option>";
					foreach ($categories as $category) {
						echo "<option value='{$category->id}'>{$category->name}</option>";
					}
				?>
		</select>
		<span> Ou seulement le projet</span>
		<select name="projects" id="projectsCombo" onchange="projectFilter(this)" >
				<?php
					echo "<option value='' selected=\"selected\">Tout</option>";
					foreach ($projects as $project) {
						echo "<option value='{$project->id}'>{$project->title}</option>";
					}
				?>
		</select>

<?php
			foreach ($projects as $project) {
				$releases = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `project_id` = '%d' ORDER BY `volume` ASC, `chapter` ASC, `subchapter` ASC, `type` ASC", $project->id));
				
				if ($releases) {
?>
					<div class="project cat<?php echo $project->category; ?>" id="proj<?php echo $project->id; ?>">
					<br> &nbsp; <a href="admin.php?page=manga/project&action=edit&id=<?php echo $project->id; ?>" style="text-decoration: none; font-weight: bold"><?php echo $project->title; ?></a> &nbsp; <?php if ($project->title_alt) echo "&#12302;{$project->title_alt}&#12303;"; ?>
					<table class="wp-list-table widefat fixed">
						<thead>
							<th scope="col" width="100px">Date</th>
							<th scope="col">Release</th>
							<th scope="col" width="150px">Action</th>
						</thead>
						
						<tbody id="the-list">
							<?php $row = 1; ?>
							<?php foreach ($releases as $release) {
									$class = "";
									if ($publishedstatus != 0 && $release->status == $publishedstatus) 
										$class = "finished";
										?>
							<tr<?php if ($row % 2) $class.=" alternate";
									echo " class=\"{$class}\""; $row++ ?>>
								<td><?php echo date('Y.m.d', $release->unixtime); ?></td>
								<td><?php echo get_sFormatRelease($project, $release); if ($release->title) echo ' - <i>' . $release->title . '</i>'; ?></td>
								<td>
									<a href="admin.php?page=manga/release&action=edit&id=<?php echo $release->id; ?>" title="Éditer la Release">Éditer</a> | 
									<a href="admin.php?page=manga/release&action=delete&id=<?php echo $release->id; ?>" title="Effacer la Release">Effacer</a> | 
									<a href="<?php echo get_sPermalink($release->project_id); ?>#release-<?php echo $release->id; ?>" title="Afficher la Release">Afficher</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					</div>
<?php
				}
			}
?>
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

/* EOF: admin/menu_release.php */