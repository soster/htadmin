<?php
/**
 * Page model:
 * $users as an array of strings
 * $use_metadata true / false
 * $meta_map as an array of meta_model objects, index is the user name.
 */
?>
<?php

if (count ( $users ) == 0) {
	echo "<p>No users found!</p>";
} else {
	?>
<div class="panel panel-default">

	<table class="table">
		<thead>
			<tr>
				<th>Username</th>
			<?php
	if ($use_metadata) {
		?>
			<th>Email</th>
				<th>Name</th>
			<?php
	}
	?>
			<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
	<?php
	
	foreach ( $users as $user ) {
		if ($user == null || trim($user) ==='') {
			continue;
		}
		// JS string literals via json_encode, HTML-escaped for the attribute.
		$js_user = htmlspecialchars ( json_encode ( $user, JSON_UNESCAPED_SLASHES ), ENT_QUOTES, 'UTF-8' );
		if ($use_metadata && $meta_map != null && array_key_exists($user,$meta_map)) {
			$js_email = htmlspecialchars ( json_encode ( $meta_map [$user]->email, JSON_UNESCAPED_SLASHES ), ENT_QUOTES, 'UTF-8' );
			$js_name = htmlspecialchars ( json_encode ( $meta_map [$user]->name, JSON_UNESCAPED_SLASHES ), ENT_QUOTES, 'UTF-8' );
			$fieldjs = "onclick=\"setUserField(" . $js_user . ", " . $js_email . ", " . $js_name . ");\"";
		} else {
			$fieldjs = "onclick=\"setUserField(" . $js_user . ", '', '');\"";
		}
		$html_user = htmlspecialchars ( $user, ENT_QUOTES, 'UTF-8' );
		$js_del = htmlspecialchars ( json_encode ( $user, JSON_UNESCAPED_SLASHES ), ENT_QUOTES, 'UTF-8' );

		echo "<tr class='id-" . $html_user . "' >";
		echo "<td scope='row' " . $fieldjs . ">" . $html_user . " </td>";
		if ($use_metadata && isset ( $meta_map [$user] )) {
			echo "<td scope='row'>" . htmlspecialchars ( $meta_map [$user]->email, ENT_QUOTES, 'UTF-8' ) . "</td>";
			echo "<td scope='row'>" . htmlspecialchars ( $meta_map [$user]->name, ENT_QUOTES, 'UTF-8' ) . "</td>";
		} else {
			echo "<td scope='row'> </td>";
			echo "<td scope='row'> </td>";
		}
		echo "<td scope='row'><a class='btn btn-danger pull-right' " . "onclick=\"deleteUser(" . $js_del . ");\"" . "href='#' >Delete</a>" . "</li></td>";
	}
	?>
	</tbody>
	</table>

</div>
<p>Click on a user to edit.</p>
<?php
}
?>
