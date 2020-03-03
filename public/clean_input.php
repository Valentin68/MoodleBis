<?php

function clean_input($data) {
		$data = htmlspecialchars($data);
		return $data;
	}

?>