<?php $this->layout("layouts/base", ['title' => 'Landing Page']); ?>

<style>
	.container-a {
	    height: 100px;
	    text-align: center;  /* align the inline(-block) elements horizontally */
	    font: 0/0 a;         /* remove the gap between inline(-block) elements */
	}

	.container-a:before {    /* create a full-height inline block pseudo=element */
	    content: ' ';
	    display: inline-block;
	    vertical-align: middle;  /* vertical alignment of the inline element */
	    height: 100%;
	}

	#element {
	    display: inline-block;
	    vertical-align: middle;  /* vertical alignment of the inline element */
	    font: 16px/1 Arial sans-serif;        /* <-- reset the font property */
	}
</style>

<div class="container-a">
    <div id="element" style="margin-top: 20%;">
    	<img src="<?php echo APP_ROOT; ?>/logo.png" style="width: 100px; height: auto;" />
    </div>
</div>

<!-- <div style="">
	<img src="<?php echo APP_ROOT; ?>/logo.png" alt="" style="width: 100px; height: auto; position:absolute;
    top:0;
    bottom:0;
    margin:auto;">
</div> -->
