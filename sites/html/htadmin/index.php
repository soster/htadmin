<?php
include_once ('includes/checklogin.php');
include_once ('tools/htpasswd.php');
include_once ('includes/head.php');
include_once ('includes/nav.php');

$htpasswd = new htpasswd ( $ini ['secure_path'], true);
$use_metadata = $ini ['use_metadata'];

?>

<div class="container box">
	<div class="row">
		<div class="col-xs-12">
<?php

echo "<h2>" . $ini ['app_title'] . "</h2>";

if (isset ( $_POST ['user'] )) {
	$username = $_POST ['user'];
	$passwd = $_POST ['pwd'];

	if (!check_username($username) || !check_password_quality($passwd)) {
		?>
			<div class="alert alert-danger">
			<?php
		echo "<p>User <em>" . htmlspecialchars ( $username ) . "</em> is invalid!.</p>";
	} else {
		?>
			<div class="alert alert-info">
			<?php
		if (! $htpasswd->user_exists ( $username )) {
			$htpasswd->user_add ( $username, $passwd );
			echo "<p>User <em>" . htmlspecialchars ( $username ) . "</em> created.</p>";
		} else {
			$htpasswd->user_update ( $username, $passwd );
			echo "<p>User <em>" . htmlspecialchars ( $username ) . "</em> changed.</p>";
		}
	}
	

	?>
		</div>
    <?php
}
?>
<div class="result alert alert-info" style="display: none;"></div>

		</div>
	</div>
	<div class=row>
		<div class="col-xs-12 col-md-4">
			<h3>Create or change user and password:</h3>
			<form class="navbar-form navbar-left" action="index.php"
				method="post">
				<div class="form-group">

					<input type="text" class="userfield form-control"
						placeholder="Username" name="user">
					</p>
					<p>
						<input class="passwordfield form-control" type="password"
							name="pwd" placeholder="Password" />
					</p>
					<button type="submit" class="btn btn-default">Submit</button>
				</div>
			</form>

		</div>

		<div class="col-xs-12 col-md-6">
			<h3>Users found:</h3>
			<ul class="list-group">
			<?php
			$users = $htpasswd->get_users ();
			if ($use_metadata) {
				$meta_map = $htpasswd->get_metadata();
			}
			
			foreach ( $users as $user ) {
				echo "<li class='list-group-item list-item-with-button id-" . htmlspecialchars ( $user ) . 
				" ' onclick=\"setUserField('" . htmlspecialchars ( $user ) . "');\">" . 
				htmlspecialchars ( $user ) . " ";
				if ($use_metadata && isset ($meta_map[$user])) {
					echo $meta_map[$user]->email . " " .
					$meta_map[$user]->name . " ";
				}				
				"<a class='btn btn-danger btn-list-item pull-right' " . 
				"onclick=\"deleteUser('" . htmlspecialchars ( $user ) . "');\"" . "href='#' >Delete</a>" . "</li>\n";
			}
			?>
			</ul>
		</div>
	</div>
	<div class=row>
	<br/><br/>
		<div class="col-xs-12 col-md-10 well">
			<p>Create new users for the htpasswd file here. A user can change his/her password with this <a href="selfservice.php">self service link.</a><br/>
			You can fill the username in the form if you add the url parameter user=&lt;username&gt;</p>
		</div>
	</div>
</div>
<?php
include_once ('includes/footer.php');
?>
