<?php
$boxes = array(
        // sizes are styled through css and top, left css attributes are hard-coded on the div
        array('size' => 3, 'top' => 0, 'left' => 0),
        array('size' => 2, 'top' => 0,'left' => 224),
        array('size' => 2, 'top' => 78, 'left' => 224),
        array('size' => 3, 'top' => 0, 'left' => 336),
        array('size' => 2, 'top' => 0, 'left' => 560),
        array('size' => 2, 'top' => 78, 'left' => 560),
        array('size' => 2, 'top' => 156, 'left' => 0),
        array('size' => 2, 'top' => 226, 'left' => 0),
        array('size' => 3, 'top' => 156, 'left' => 112)
        /*
        array('size' => 2, 'top' => 156, 'left' => 0),
        array('size' => 3, 'top' => 156, 'left' => 112),
        array('size' => 2, 'top' => 156, 'left' => 336),
        array('size' => 1, 'top' => 156, 'left' => 448),
        array('size' => 1, 'top' => 195, 'left' => 448),
        array('size' => 2, 'top' => 156, 'left' => 504),
        array('size' => 1, 'top' => 156, 'left' => 616),
        array('size' => 1, 'top' => 234, 'left' => 56),
        array('size' => 1, 'top' => 234, 'left' => 336)
         */
);
?>
<h2 class="pheader text-center">Careers</h2>
<p class="text-center">CultureMesh is building networks to match the real-world dynamics of people living, working, and traveling outside of their home countries to knit the diverse fabrics of our world together.</p>
<a href="" class="btn cm-button center-elem btn-big">See Current Openings</a>

<div id="js-container">
<?php for($i=0;$i<count($boxes);$i++):
    $box = $boxes[$i];?>
    <div class="js-image size-<?php echo $box['size']; ?>" style="top:<?php echo $box['top']; ?>px;left:<?php echo $box['left']; ?>px;">		
        <a href="#wolf!"><img class="js-small-image" src="images/wolf-moon.jpg"/></a>
        <div class="js-small-caption">
            <span>We're friendly.</span>
        </div>
        <div class="js-overlay-caption-content">
            <h4>Blah image title</h4>
            <p>Random stuff that'll match each picture...blah</p>
        </div>
    </div>
<?php endfor; ?>
</div>

<div class="bottom-info">
    <h2 class="pheader text-center">Current Openings</h2>
    <ul class="three-section-pics">
        <li class="careers_box_engineering">
            <div class="over">
            <h3 class="pheader careers_box_head">Engineering</h3>
            <a href="" class="btn cm-button careers_box_btn">See Openings</a>
            </div>
        </li>
        <li class="careers_box_creative">
            <div class="over">
            <h3 class="pheader careers_box_head">Creative</h3>
            <a href="" class="btn cm-button careers_box_btn">See Openings</a>
            </div>
        </li>
        <li class="careers_box_business">
            <div class="over">
            <h3 class="pheader careers_box_head">Business</h3>
            <a href="" class="btn cm-button careers_box_btn">See Openings</a>
            </div>
        </li>
    </ul>
</div>