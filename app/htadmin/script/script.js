function deleteUser(user) {
	if (confirm('Are you sure?')) {
		$.post("delete.php", { user: user }, function (data) {
			$(".result").html(data);

			if (data == "success") {
				$(".result").html("<p>User <em>" + $("<span>").text(user).html() + "</em> deleted.</p>");
				$(".result").show("fast");
				// escape the value for CSS selector use; usernames may contain special chars
				var esc = (window.CSS && CSS.escape) ? CSS.escape(user) : user.replace(/[\\"'\s.#:]/g, '\\$&');
				$(".id-" + esc).remove();
			} else {
				$(".result").html("<p>An error occured.</p>");
			}
		});
	}
}

function setUserField(user, email, name) {
	$(".userfield").val(user);
	$(".emailfield").val(email);
	$(".namefield").val(name);
	$(".passwordfield").focus();
}
