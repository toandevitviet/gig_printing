<?php 
$class_banner = 'banners';
foreach ($banners as $banner) {
		if($banner['name'] == '16'):
			$class_banner = 'top-banner';
		else:
			$class_banner = 'banners';
		endif;
	 } 	
?>

<div id="banner<?php echo $module; ?>" class="banner container <?php echo $class_banner; ?>">
		<div class="row">
			<?php foreach ($banners as $banner) { ?>
			<?php if ($banner['link']) { ?>
			<div class="col-sm-12" id="banner-adv">
				<a href="<?php echo $banner['link']; ?>">
					<img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" />
					<div class="s-desc"><?php echo $banner['description']; ?></div>
				</a>
			</div>
			<?php } else { ?>
			<div class="col-sm-12" id="banner-adv">
				<img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" />
				<div class="s-desc"><?php echo $banner['description']; ?></div>
			</div>
			<?php } ?>
			<?php } ?>
		</div>
</div>
<div class="clear"></div>