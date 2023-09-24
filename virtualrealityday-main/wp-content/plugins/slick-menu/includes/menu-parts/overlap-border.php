<?PHP

// SM OVERLAP BORDER

$return = "";

if(!empty($levelAnimation) && $levelAnimation === 'overlap') {
	$return .= '<div class="sm-level-overlap"></div>';
}	

return $return;