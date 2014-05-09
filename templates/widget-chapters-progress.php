<?php

/**
 * WPPB Get Progress Bar
 * gets all the parameters passed to the shortcode and constructs the progress bar
 * @param $status - Status of the release
 * @param $publishedstatus - Status considered as "complete"/"published"
 * @param $text - Text to be displayed inside the progress bar
 * @author Chris Reynolds
 * @since 2.0
 */
function chapterspb_get_progress_bar($status, $publishedstatus, $text) {

	$percentage = ($status+1) / $publishedstatus * 100;
	$width = $percentage . "%";
	
	/**
	 * here's the html output of the progress bar
	 */
	$wppb_output	= "<div class=\"wppb-wrapper inside\" >"; 
	$wppb_output .= "<div class=\"inside\">";
	$wppb_output .= $text;
	$wppb_output .= "</div>";
	$wppb_output 	.= 	"<div class=\"wppb-progress\">";
	$wppb_output	.= "<span class=\"candystripes\"";
	
	if ($status <=1)
		$color="red";
	else
		$color="green";
	$wppb_output .= " style=\"width: {$width}; background: {$color};\"";
	$wppb_output	.= "><span></span></span>";
	$wppb_output	.=	"</div>";
	$wppb_output	.= "</div>";
	/**
	 * now return the progress bar
	 */
	return $wppb_output;
}
?>

<div class="chapters-progress-list">
	<?php
	global $wpdb;
	$publishedstatus = wpmanga_get('wpmanga_release_statuspublished',0);
	if ($publishedstatus != 0)
	{
		if ($projectCategory)
			$projects = get_sListCategory($projectCategory);
		
		
		$sql ="	
		SELECT rel.*, p.title AS project_title
		FROM `{$wpdb->prefix}projects_releases` AS rel
		INNER JOIN `{$wpdb->prefix}projects` AS p ON rel.project_id = p.id
		INNER JOIN (
			SELECT project_id, MIN(`chapter`) AS chapter
			FROM `{$wpdb->prefix}projects_releases`
			WHERE `status` <> {$publishedstatus}
			AND `type` = 0";
			if ($projects)
			{
				$ids = "";
				$first = true;
				foreach ($projects as $project)
				{
					if (!$first)
						$ids .=",";
					$ids.=$project->id;
					$first =false;
				}
				
				$sql .= "
				AND `project_id` IN ({$ids})";
			}
			$sql .="
			GROUP BY `project_id`
		) AS r ON rel.project_id = r.project_id and rel.chapter = r.chapter
		ORDER BY project_title ASC";
		
		$chaps = $wpdb->get_results( $wpdb->prepare($sql,$limit));
		
		foreach ($chaps as $chap)
		{
			echo chapterspb_get_progress_bar($chap->status,$publishedstatus, "$chap->project_title $chap->chapter");
		}
	}
	?>
</div>