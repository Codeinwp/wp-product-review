<div class="user-comments-grades">
<?php
	foreach ( $options as $k => $option ) {
		$intGrade = intval( $option['value'] * 10 );
?>
	<div class="comment-meta-option">
		<p class="comment-meta-option-name"><?php echo $option['name'];?></p>
		<p class="comment-meta-option-grade"><?php echo $option['value'];?></p>
        <div class="cwpr_clearfix"></div>
        <div class="comment-meta-grade-bar <?php echo $review->get_rating_class( $intGrade );?>">
			<div class="comment-meta-grade" style="width: <?php echo $intGrade;?>%"></div>
		</div><!-- end .comment-meta-grade-bar -->
	</div><!-- end .comment-meta-option -->
<?php
	}
?>
</div>

<?php echo $text; ?>

<div class="cwpr_clearfix"></div>
