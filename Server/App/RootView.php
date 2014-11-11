<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================
defined('MUDPUPPY') or die('Restricted');

require_once("aws.phar");

use Mudpuppy\App;

?>

<div class="top-bar-holder">
	<nav class="top-bar row" data-topbar>
		<ul class="title-area">
			<li class="name">
				<h1><a href="#">AWS re:Invent Hackathon - NASA JPL Challenge</a></h1>
			</li>
			<li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
		</ul>
		<section class="top-bar-section">
			<ul class="right">
				<li><a href="http://www.jpl.nasa.gov/">NASA JPL</a></li>
			</ul>
		</section>
	</nav>
</div>

<div class="logo" tabindex="-1">
	<div class="row">
		<div class="large-12 columns">
			<img src="content/images/logo_nasa_trio_black.png" />
			<span class="right tagLine">Leading robotic exploration of the solar system.</span>
		</div>
	</div>
</div>

<div ng-app="imagesApp">
	<div ng-view>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.modal').keydown(function(e){
			if(e.keyCode === 9){
				id = $(this).attr('data-reveal-id');
				$("#"+id).children(".firstTab").first().focus();

			}
		})
		$('.close-reveal-modal').keydown(function (e){
		    if(e.keyCode === 13){
		        $(this).click();
		    }
		    if(e.keyCode === 9){
				id = $(this).parent();
				$(id).children(".firstTab").first().focus();

			}
		});

	});
</script>


